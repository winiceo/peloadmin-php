<?php

declare(strict_types=1);



namespace Leven\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use function Leven\validateUsername;
use Leven\Packages\Wallet\TypeManager;
use function Leven\validateChinaPhoneNumber;
use Leven\Packages\Wallet\TargetTypeManager;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        // 注册验证规则.
        $this->registerValidator();
    }

    /**
     * Resgister the application service.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function register()
    {
        $this->app->singleton('cdn', function ($app) {
            return new \Leven\Cdn\UrlManager($app);
        });

        $this->app->singleton(TypeManager::class, function ($app) {
            return new TypeManager($app);
        });

        $this->app->singleton(TargetTypeManager::class, function ($app) {
            return new TargetTypeManager($app);
        });

        $this->registerMorpMap();
    }

    /**
     * 注册验证规则.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function registerValidator()
    {
        // 注册中国大陆手机号码验证规则
        $this->app->validator->extend('cn_phone', function (...$parameters) {
            return validateChinaPhoneNumber($parameters[1]);
        });

        // 注册用户名验证规则
        $this->app->validator->extend('username', function (...$parameters) {
            return validateUsername($parameters[1]);
        });

        // 注册显示长度验证规则
        $this->app->validator->extend('display_length', function ($attribute, string $value, array $parameters) {
            unset($attribute);

            return $this->validateDisplayLength($value, $parameters);
        });
    }

    /**
     * 验证显示长度计算.
     *
     * @param strint|int $value
     * @param array $parameters
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function validateDisplayLength(string $value, array $parameters): bool
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Parameters must be passed');
        // 补充 min 位.
        } elseif (count($parameters) === 1) {
            $parameters = [0, array_first($parameters)];
        }

        list($min, $max) = $parameters;

        preg_match_all('/[a-zA-Z0-9_]/', $value, $single);
        $length = count($single[0]) / 2 + mb_strlen(preg_replace('([a-zA-Z0-9_])', '', $value));

        return $length >= $min && $length <= $max;
    }

    /**
     * Register model morp map.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function registerMorpMap()
    {
        $this->setMorphMap([
            'users' => \Leven\Models\User::class,
            'comments' => \Leven\Models\Comment::class,
        ]);
    }

    /**
     * Set the morph map for polymorphic relations.
     *
     * @param array|null $map
     * @param bool|bool $merge
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    private function setMorphMap(array $map = null, bool $merge = true)
    {
        Relation::morphMap($map, $merge);
    }
}
