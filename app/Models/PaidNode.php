<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;

class PaidNode extends Model
{
    use Relations\PaidNodeHasUser;
}
