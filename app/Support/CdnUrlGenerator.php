<?php

declare(strict_types=1);



namespace Leven\Support;

use Leven\Models\File;
use Leven\Contracts\Cdn\UrlGenerator as FileUrlGeneratorContract;

abstract class CdnUrlGenerator implements FileUrlGeneratorContract
{
    /**
     * File data model.
     *
     * @var \Leven\Models\File
     */
    protected $file;

    /**
     * Get file data model.
     *
     * @return \Leven\Models\File
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getFile(): File
    {
        return $this->file;
    }

    /**
     * Set file data model.
     *
     * @param \Leven\Models\File $file
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }
}
