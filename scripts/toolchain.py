#!/usr/bin/env python3
"""Pinned CLI tools for local checks and CI. Requires only Python 3.8+ to start."""
import argparse
import hashlib
import json
import os
from pathlib import Path
import platform
import re
import shutil
import subprocess
import sys
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
CONFIG = json.loads((ROOT / '.toolchain.json').read_text())
SETUP = 'python3 scripts/toolchain.py setup'


def capture(command, root=ROOT, env=None):
    result = subprocess.run(command, cwd=root, env=env, stdout=subprocess.PIPE,
                            stderr=subprocess.PIPE, text=True, timeout=30)
    if result.returncode:
        raise ValueError(result.stderr.strip() or result.stdout.strip() or str(command))
    return result.stdout.strip()


def tool_dir(root=ROOT):
    # Linked worktrees share downloaded tools, but keep their own npm/vendor trees.
    common = Path(capture(['git', 'rev-parse', '--git-common-dir'], root))
    return (root / common).resolve().parent / '.build' / 'toolchain'


def environment(root=ROOT):
    env = dict(os.environ, PYTHONDONTWRITEBYTECODE='1')
    if env.get('GITHUB_ACTIONS') == 'true':
        return env  # Actions install from the same manifest, with no local override.
    base = tool_dir(root)
    bins = [base / name / CONFIG[name]['version'] / suffix
            for name, suffix in [('php', 'bin'), ('node', 'bin'), ('composer', '')]]
    paths = [str(p) for p in bins if p.is_dir()]
    if env.get('PATH'):
        paths.append(env['PATH'])
    env['PATH'] = os.pathsep.join(paths)
    return env


def versions(output=None):
    values = {name: CONFIG[name]['version'] for name in ('php', 'node', 'composer')}
    values['npm'] = CONFIG['node']['npm']
    text = ''.join('{}={}\n'.format(key, value) for key, value in values.items())
    if output:
        with output.open('a') as handle:
            handle.write(text)
    print(text, end='')


def doctor(full=True, root=ROOT, env=None):
    env = environment(root) if env is None else env
    failures = []

    def probe(name, command, expected=None, extract_version=False):
        try:
            actual = capture(command, root, env)
            if extract_version:
                match = re.search(r'\b\d+\.\d+\.\d+\b', actual)
                actual = match.group(0) if match else actual
            if expected is not None and actual != expected:
                raise ValueError('found {}; required {}'.format(actual, expected))
            print('OK   {}: {}'.format(name, actual), flush=True)
        except (ValueError, OSError, subprocess.TimeoutExpired) as exc:
            failures.append(name)
            print('FAIL {}: {}'.format(name, exc), flush=True)

    print('Toolchain: {} {}'.format(platform.system(), platform.machine()), flush=True)
    for executable in ('git', 'bash', 'rg', 'python3'):
        if not shutil.which(executable, path=env.get('PATH')):
            failures.append(executable)
            print('FAIL missing {} on PATH'.format(executable), flush=True)
    if sys.version_info < (3, 8):
        failures.append('Python >= 3.8')
    probe('PHP', ['php', '-r', 'echo PHP_VERSION;'], CONFIG['php']['version'])
    if full:
        probe('Node', ['node', '-p', 'process.versions.node'], CONFIG['node']['version'])
        probe('npm', ['npm', '--version'], CONFIG['node']['npm'])
        probe('Composer', ['composer', '--version', '--no-ansi', '--short'],
              CONFIG['composer']['version'], extract_version=True)
        probe('PHP extensions', ['php', '-r',
              '$missing=array_filter(explode(",", "curl,dom,filter,mbstring,openssl,Phar,SimpleXML,tokenizer,xml,xmlwriter,zlib"),'
              ' fn($e)=>!extension_loaded($e)); if($missing){fwrite(STDERR,implode(",",$missing));exit(1);} echo "ready";'])
        probe('Node dependencies', ['node', '-e',
              'const p=require("./package.json"); const lock=require("./package-lock.json");'
              'for(const [name,want] of Object.entries(p.devDependencies)) {'
              'const got=require(name+"/package.json").version;'
              'if(got!==want || lock.packages["node_modules/"+name].version!==want) throw Error(name+": run npm ci");'
              '} console.log("locked versions installed");'])
        phar = env.get('PHPSTAN_PHAR', 'vendor/phpstan/phpstan/phpstan.phar')
        lock = json.loads((root / 'composer.lock').read_text())
        phpstan = next(p['version'].lstrip('v') for p in lock['packages-dev'] if p['name'] == 'phpstan/phpstan')
        probe('PHPStan', ['php', phar, '--version'], phpstan, extract_version=True)
        probe('WordPress stubs', ['php', '-r',
              'if(!is_readable("vendor/php-stubs/wordpress-stubs/wordpress-stubs.php")){exit(1);} echo "installed";'])
        # Ask both test configs: navigation may explicitly override the browser.
        probe('Browser', ['node', '-e',
              'const fs=require("node:fs"); const {chromium}=require("@playwright/test");'
              'const paths=["forms","navigation"].map(n=>require("./scripts/tests/"+n+".config.cjs")'
              '.use.launchOptions.executablePath || chromium.executablePath());'
              'for(const p of paths) if(!fs.existsSync(p)) throw Error("Missing browser: "+p);'
              'console.log([...new Set(paths)].join(", "));'])
    if failures:
        print('Environment incomplete. Run `{}`; prerequisites: docs/development/TOOLCHAIN.md'.format(SETUP), flush=True)
    return 1 if failures else 0


def download(url, destination, digest):
    destination.parent.mkdir(parents=True, exist_ok=True)
    if not destination.exists():
        print('Download: ' + url, flush=True)
        temporary = destination.with_suffix(destination.suffix + '.partial')
        with urllib.request.urlopen(url, timeout=60) as response, temporary.open('wb') as handle:
            shutil.copyfileobj(response, handle)
        temporary.replace(destination)
    if hashlib.sha256(destination.read_bytes()).hexdigest() != digest:
        raise ValueError('Checksum mismatch: {}. Remove this cached file and retry.'.format(destination))
    return destination


def setup(root=ROOT):
    base = tool_dir(root)
    env = environment(root)
    target = '{}-{}'.format(platform.system().lower(),
                           {'x86_64': 'x64', 'aarch64': 'arm64'}.get(platform.machine(), platform.machine()))
    if target not in CONFIG['node']['sha256']:
        raise ValueError('Unsupported installer platform: ' + target)
    for binary in ('git', 'bash', 'rg', 'tar', 'make', 'cc', 'pkg-config', 'unzip'):
        if not shutil.which(binary, path=env['PATH']):
            raise ValueError('Missing prerequisite: {}. See docs/development/TOOLCHAIN.md'.format(binary))

    node_version = CONFIG['node']['version']
    node_name = 'node-v{}-{}'.format(node_version, target)
    node = base / 'node' / node_version
    if not (node / 'bin/node').is_file():
        archive = download('https://nodejs.org/dist/v{}/{}.tar.xz'.format(node_version, node_name),
                           base / 'cache' / (node_name + '.tar.xz'), CONFIG['node']['sha256'][target])
        node.parent.mkdir(parents=True, exist_ok=True)
        subprocess.run(['tar', '-xf', str(archive), '-C', str(node.parent)], check=True)
        (node.parent / node_name).rename(node)

    php_version = CONFIG['php']['version']
    php = base / 'php' / php_version
    if not (php / 'bin/php').is_file():
        archive = download('https://www.php.net/distributions/php-{}.tar.xz'.format(php_version),
                           base / 'cache' / ('php-{}.tar.xz'.format(php_version)), CONFIG['php']['sha256'])
        build = base / 'build'
        build.mkdir(parents=True, exist_ok=True)
        subprocess.run(['tar', '-xf', str(archive), '-C', str(build)], check=True)
        source = build / ('php-' + php_version)
        iconv = '--with-iconv'
        if platform.system() == 'Darwin':
            sdk = capture(['xcrun', '--show-sdk-path'])
            iconv += '=' + sdk + '/usr'
            brew = shutil.which('brew')
            if brew:
                openssl = capture([brew, '--prefix', 'openssl@3'])
                env['PKG_CONFIG_PATH'] = openssl + '/lib/pkgconfig:' + env.get('PKG_CONFIG_PATH', '')
        options = ['--prefix=' + str(php), '--disable-all', '--enable-cli', '--disable-cgi',
                   '--disable-phpdbg', '--enable-phar', '--enable-filter', '--enable-tokenizer',
                   '--enable-mbstring', '--disable-mbregex', '--enable-fileinfo', '--enable-ctype',
                   '--enable-session', '--enable-posix', '--enable-dom', '--enable-xml',
                   '--enable-simplexml', '--enable-xmlreader', '--enable-xmlwriter', '--with-libxml',
                   '--with-openssl', iconv, '--enable-pcntl', '--with-zlib', '--with-curl', '--without-pear']
        log_path = base / 'php-build.log'
        print('Build PHP {} (first setup takes several minutes). Log: {}'.format(php_version, log_path), flush=True)
        with log_path.open('w') as log:
            for command in [['./configure', *options], ['make', '-j{}'.format(min(os.cpu_count() or 2, 8))],
                            ['make', 'install']]:
                result = subprocess.run(command, cwd=source, env=env, stdout=log, stderr=subprocess.STDOUT)
                if result.returncode:
                    raise ValueError('PHP build failed. See ' + str(log_path))

    composer = base / 'composer' / CONFIG['composer']['version']
    phar = download('https://getcomposer.org/download/{}/composer.phar'.format(CONFIG['composer']['version']),
                    composer / 'composer.phar', CONFIG['composer']['sha256'])
    phar.chmod(0o755)
    if not (composer / 'composer').exists():
        (composer / 'composer').symlink_to('composer.phar')
    env = environment(root)
    env['COMPOSER_HOME'] = str(base / 'cache' / 'composer')
    env['npm_config_cache'] = str(base / 'cache' / 'npm')
    for command in [['npm', 'ci'], ['composer', 'install', '--no-interaction', '--no-progress', '--prefer-dist']]:
        subprocess.run(command, cwd=root, env=env, check=True)
    chrome = Path('/Applications/Google Chrome.app/Contents/MacOS/Google Chrome')
    if not chrome.is_file():
        subprocess.run(['npx', '--no-install', 'playwright', 'install', 'chromium'], cwd=root, env=env, check=True)
    return doctor(root=root, env=env)


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    commands = parser.add_subparsers(dest='command', required=True)
    version_args = commands.add_parser('versions')
    version_args.add_argument('--github-output', type=Path)
    doctor_args = commands.add_parser('doctor')
    doctor_args.add_argument('--basic', action='store_true', help='docs/agents prerequisites only')
    commands.add_parser('setup')
    execute = commands.add_parser('exec', help='run a command using project tools')
    execute.add_argument('argv', nargs=argparse.REMAINDER)
    args = parser.parse_args()
    try:
        if args.command == 'versions':
            versions(args.github_output)
            return 0
        if args.command == 'doctor':
            return doctor(full=not args.basic)
        if args.command == 'setup':
            return setup()
        argv = args.argv[1:] if args.argv[:1] == ['--'] else args.argv
        if not argv:
            raise ValueError('exec requires a command')
        return subprocess.run(argv, cwd=ROOT, env=environment()).returncode
    except (ValueError, OSError, subprocess.SubprocessError) as exc:
        print('FAIL: {}'.format(exc), file=sys.stderr)
        return 1


if __name__ == '__main__':
    sys.exit(main())
