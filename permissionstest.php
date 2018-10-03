#!/usr/bin/env php
<?php

$testInstance = new TestPermissionSet();
try {
    if ($testInstance->test()) {
        echo "\nSuccess!\n";
    }
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

class TestPermissionSet
{
    private $exceptions = [
    ];

    private $setup;

    private $permissionSet;

    /**
     * @return bool
     * @throws Exception
     */
    public function test(): bool
    {
        $result = true;

        echo "Absent classes:\n\n";
        $classNames = $this->getNames('classes', 4);
        $classAccesses = $this->getAccesses('classAccesses', 'enabled', 'apexClass');
        foreach ($classNames as $className) {
            if (!$this->checkAccessibility($className, $classAccesses)) {
                echo "$className\n";
                $result = false;
            }
        }

        echo "\nAbsent pages:\n\n";
        $pagesNames = $this->getNames('pages', 5);
        $pagesAccesses = $this->getAccesses('pageAccesses', 'enabled', 'apexPage');

        foreach ($pagesNames as $pagesName) {
            if (!$this->checkAccessibility($pagesName, $pagesAccesses)) {
                echo "$pagesName\n";
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param $folder
     * @param $extensionLength
     * @return array
     * @throws Exception
     */
    private function getNames(string $folder,int $extensionLength): array
    {
        $names = [];
        $setup = $this->getSetup();
        $allNames = scandir("{$setup['path']}/$folder");
        foreach ($allNames as $name) {
            if (is_file("{$setup['path']}/$folder/$name") && (substr($name, -4) != '.xml')) {
                $names[] = substr($name, 0, -$extensionLength);
            }
        }
        return $names;
    }

    /**
     * @param string $root
     * @param string $enabled
     * @param string $name
     * @return array
     * @throws Exception
     */
    private function getAccesses(string $root, string $enabled, string $name): array
    {
        $accesses = [];
        $permissionSet = $this->getPermissionSet();
        foreach ($permissionSet[$root] as $access) {
            if ($access[$enabled] == 'true') {
                $accesses[] = $access[$name];
            }
        }
        return $accesses;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getPermissionSet(): array
    {
        if (is_null($this->permissionSet)) {
            $setup = $this->getSetup();
            $permissionSet = new SimpleXMLElement(file_get_contents($setup['file']));
            $this->permissionSet = json_decode(json_encode($permissionSet), true);
        }
        return $this->permissionSet;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getSetup(): array
    {
        if (is_null($this->setup)) {
            if (!file_exists('setup.json')) {
                throw new \Exception('Setup file not found!');
            }
            $this->setup = json_decode(file_get_contents('setup.json'), true);
            if (!(isset($this->setup['file']) && isset($this->setup['path']))) {
                throw new \Exception('Incorrect setup format!');
            }
        }
        return $this->setup;
    }

    /**
     * @param string $className
     * @param array $classAccesses
     * @return bool
     */
    private function checkAccessibility(string $className, array $classAccesses): bool
    {
        if (in_array($className, $this->exceptions)) {
            return true;
        }
        if (!in_array($className, $classAccesses)) {
            return false;
        }
        return true;
    }
}