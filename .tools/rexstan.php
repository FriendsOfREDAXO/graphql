<?php

$host = '127.0.0.1';
$user = 'root';
$password = 'root';
$dbname = 'redaxo5';
$sql = null;

$rexstanLevel = 9;
$rexstanExtensions = [
    realpath(__DIR__ . '/../../rexstan/config/rex-superglobals.neon'),
    realpath(__DIR__ . '/../../rexstan/vendor/phpstan/phpstan/conf/bleedingEdge.neon'),
    realpath(__DIR__ . '/../../rexstan/vendor/phpstan/phpstan-strict-rules/rules.neon'),
    realpath(__DIR__ . '/../../rexstan/vendor/phpstan/phpstan-deprecation-rules/rules.neon'),
    realpath(__DIR__ . '/../../rexstan/config/phpstan-phpunit.neon'),
    realpath(__DIR__ . '/../../rexstan/config/phpstan-dba.neon'),
    realpath(__DIR__ . '/../../rexstan/config/cognitive-complexity.neon'),
    realpath(__DIR__ . '/../../rexstan/config/code-complexity.neon'),
    dirname(dirname(__DIR__)) . '/rexstan/config/dead-code.neon'
];

try {
    $connection = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

    $statement = $connection->prepare("INSERT INTO rex_config (`namespace`, `key`, `value`) VALUES ('rexstan', 'addons', :addons), ('rexstan', 'extensions', :extensions), ('rexstan', 'level', :level), ('rexstan', 'phpversion', :phpversion)");
    $statement->execute([
        'addons' => '"|' . realpath(__DIR__ . "/../") . '|"',
        'extensions' => '"|' . implode('|', $rexstanExtensions) . '|"',
        'level' => '"' . $rexstanLevel . '"',
        'phpversion' => '"80109"',
    ]);
    echo "New record created successfully";

    $connection = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $statement = $connection->query("SELECT * FROM rex_config");
    while ($row = $statement->fetch()) {
        echo $row['namespace'] . '-' . $row['namespace'] . ' : ' . $row['value'];
        echo "\n";
    }
}
catch (PDOException $e) {
    echo $sql . "\n" . $e->getMessage();
}
