<?php

$testInstance = new TestPermissionSet();
$testInstance->test();

class TestPermissionSet
{
    private $exceptions = [
    ];

    public function test(): void
    {
        $setup = $this->getSetup();
        $permissionSet = new SimpleXMLElement($setup['file']);
    }

    private function getSetup(): array
    {
        return json_decode(file_get_contents('setup.json'), true);
    }

    private function checkFile(string $filename): void
    {
        if (in_array($filename, $this->exceptions)) {
            return;
        }
    }
}