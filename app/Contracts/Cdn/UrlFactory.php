<?php

declare(strict_types=1);



namespace Leven\Contracts\Cdn;

use Leven\Models\File;

interface UrlFactory
{
    /**
     * Get URL generator.
     *
     * @param string $name
     * @return \Leven\Contracts\Cdn\UrlGenerator
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function generator(string $name = ''): UrlGenerator;

    /**
     * Make a file url.
     *
     * @param \Leven\Models\File $file
     * @param array $extra
     * @param string $name
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function make(File $file, array $extra = [], string $name = ''): string;
}
