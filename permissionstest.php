#!/usr/bin/env php
<?php

$testInstance = new TestPermissionSet();
try {
    $testInstance->test();
    echo "Success\n";
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
     * @throws Exception
     */
    public function test(): void
    {
        $classNames = $this->getClassNames();
        foreach ($classNames as $className) {
            $this->checkClass($className, $this->getClassAccesses());
        }
    }

    /**
     * @throws Exception
     */
    private function getClassNames(): array
    {
        $classNames = [];
        $setup = $this->getSetup();
        $allNames = scandir("{$setup['path']}/classes");
        foreach ($allNames as $name) {
            if (is_file("{$setup['path']}/classes/$name") && (substr($name, -4) != '.xml')) {
                $classNames[] = substr($name, 0, -4);
            }
        }
        return $classNames;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getClassAccesses(): array
    {
        $classAccesses = [];
        $permissionSet = $this->getPermissionSet();
        foreach ($permissionSet['classAccesses'] as $classAccess) {
            if ($classAccess['enabled'] == 'true') {
                $classAccesses[] = $classAccess['apexClass'];
            }
        }
        return $classAccesses;
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
     * @throws Exception
     */
    private function checkClass(string $className, array $classAccesses): void
    {
        if (in_array($className, $this->exceptions)) {
            return;
        }
        if (!in_array($className, $classAccesses)) {
            throw new Exception("$className is absent in permission set.");
        }
    }
}