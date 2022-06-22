<?php
unset($argv[0]);

$rootDir = getcwd();

$staticContentFolders = [
    $rootDir.'/var/cache',
    $rootDir.'/var/page_cache',
    $rootDir.'/var/view_preprocessed'
];

$isFrontEnd = false;
$isAdminhtml = false;
$phpVersion = 'php';
$memoryLimit = '';
foreach ($argv as $key => $arg) {
    if ( $arg == 'frontend' ) {
        $isFrontEnd = true;
    }
    if ( $arg == 'adminhtml' ) {
        $isAdminhtml = true;
    }
    if (strpos($arg, 'php') !== false) {
        unset($argv[$key]);
        $bin = explode('=',$arg);
        $phpVersion = $bin[1];
    }
    if (strpos($arg, 'php') !== false) {
        unset($argv[$key]);
        $bin = explode('=',$arg);
        $phpVersion = $bin[1];
    }
    if (strpos($arg, 'memory_limit') !== false) {
        unset($argv[$key]);
        $memoryLimit = $arg;
    }
}

if($isFrontEnd) {
    $staticContentFolders[] = $rootDir.'/pub/static/frontend';
}
if($isAdminhtml) {
    $staticContentFolders[] = $rootDir.'/pub/static/adminhtml';
}
if(!$isFrontEnd && !$isAdminhtml) {
    $staticContentFolders[] = $rootDir.'/pub/static';
}

foreach ($staticContentFolders as $staticContentFolder) {
    if(is_dir($staticContentFolder)) {
        $commands[] = "rm -rf ".$staticContentFolder.'/*';
    }
}

$arguments = implode(' ',$argv);
$commands[] = $phpVersion." ".$memoryLimit." ".$rootDir."/bin/magento"." s:s:d -f ".$arguments;

foreach ($commands as $command) {
    while (@ ob_end_flush());
    $proc = popen($command, 'r');
    while (!feof($proc))
    {
        echo fread($proc, 4096);
        @flush();
    }
}
