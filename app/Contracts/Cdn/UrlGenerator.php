<?php

declare(strict_types=1);



namespace Leven\Contracts\Cdn;

use Leven\Cdn\Refresh;

interface UrlGenerator
{
    /**
     * Generator an absolute URL to the given path.
     *
     * @param string $filename
     * @param array $extra "[float $width, float $height, int $quality]"
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function url(string $filename, array $extra = []): string;

    /**
     * Refresh the cdn files and dirs.
     *
     * @param \Leven\Cdn\Refresh $refresh
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function refresh(Refresh $refresh);
}
