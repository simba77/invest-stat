<?php

declare(strict_types=1);

namespace Modules\System\Forms\Inputs;

use LogicException;

class InputFile extends Input
{
    protected bool $multiple = false;
    protected string $uploadUrl = '';
    protected array $currentFiles = [];

    public function multiple(): static
    {
        $this->multiple = true;
        return $this;
    }

    public function setCurrentFiles(array $files): static
    {
        $this->currentFiles = $files;
        return $this;
    }

    public function setUploadUrl(string $url): static
    {
        $this->uploadUrl = $url;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        if ($this->accessDenied) {
            return [];
        }

        if (empty($this->uploadUrl)) {
            throw new LogicException('The uploadUrl is not set.');
        }

        $arr = parent::get();
        $arr['type'] = 'file';
        $arr['multiple'] = $this->multiple;
        $arr['uploadUrl'] = $this->uploadUrl;
        $arr['currentFiles'] = $this->currentFiles;
        $arr['newFiles'] = [];

        return $arr;
    }
}
