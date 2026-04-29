<?php
$lines = file(__DIR__ . '/seed_students_1005.php');
foreach ($lines as $i => $line) {
    $num = $i + 1;
    printf("%4d: %s", $num, $line);
}
