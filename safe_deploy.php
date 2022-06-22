<?php

$rootDir = getcwd();

$staticContentFolders = [
    $rootDir.'/var/cache',
    $rootDir.'/var/page_cache',
    $rootDir.'/var/view_preprocessed'
];

$isFrontEnd = false;
$isAdminhtml = false;
foreach ($argv as $arg) {
    if ( $arg == 'frontend' ) {
        $isFrontEnd = true;
    }
    if ( $arg == 'adminhtml' ) {
        $isAdminhtml = true;
    }
}
if($isFrontEnd) {
    $staticContentFolders[] = $rootDir.'/pub/static/frontend/*';
}
if($isAdminhtml) {
    $staticContentFolders[] = $rootDir.'/pub/static/adminhtml/*';
}
if(!$isFrontEnd && !$isAdminhtml) {
    $staticContentFolders[] = $rootDir.'/pub/static/*';
}

foreach ($staticContentFolders as $staticContentFolder) {
    if(is_dir($staticContentFolder)) {
        $commands[] = "rm -rf ".$staticContentFolder;
    }
}

unset($argv[0]);
$arguments = implode(' ',$argv);
$commands[] = $rootDir."/bin/magento"." s:s:d -f ".$arguments;

foreach ($commands as $command) {
    while (@ ob_end_flush());
    $proc = popen($command, 'r');
    while (!feof($proc))
    {
        echo fread($proc, 4096);
        @flush();
    }
}
