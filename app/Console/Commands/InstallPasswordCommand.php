<?php

declare(strict_types=1);



namespace Leven\Console\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Leven\Support\Configuration;

class InstallPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'install:password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Operation and installation password.';

    /**
     * The Plus config repostyory.
     *
     * @var \Leven\Support\Configuration
     */
    protected $repository;

    /**
     * Create the console command instance.
     *
     * @param \Leven\Support\Configuration $repository
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(Configuration $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * The console command handle.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function handle()
    {
        // Asking for a new password.
        $question = 'Please enter a new installation password';
        $questionDefaultValue = null;
        $value = $this->getOutput()->askHidden($question, $questionDefaultValue);

        // Ask for confirmation password and confirm whether it is consistent with inquiry password.
        $question = 'Please enter the confirmation password';
        $this->getOutput()->askHidden($question, function ($passwordConfirmation) use ($value) {
            if ($passwordConfirmation === $value) {
                return;
            }

            throw new InvalidArgumentException('Two passwords are not consistent.');
        });

        $this->repository->set('installer.password', md5($value));
        $this->info('The installation password is set successfully.');
    }
}
