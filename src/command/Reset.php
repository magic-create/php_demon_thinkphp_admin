<?php

namespace Demon\AdminThinkPHP\command;

use Demon\AdminThinkPHP\access\model\UserModel;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class Reset extends Command
{
    /**
     * The provider to publish.
     *
     * @var string
     */
    protected $provider = null;

    /**
     * The tags to publish.
     *
     * @var array
     */
    protected $tags = [];

    protected function configure()
    {
        $this
            ->setName('admin:reset')
            ->addOption('uid', null, Option::VALUE_OPTIONAL, 'account uid')
            ->addOption('username', null, Option::VALUE_OPTIONAL, 'account username')
            ->addOption('password', null, Option::VALUE_OPTIONAL, 'The default is demon', 'demon')
            ->setDescription('Reset the password for the specified account');
    }

    /**
     * Execute the console command.
     *
     * @param Input  $input
     * @param Output $output
     *
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $uid = $input->getOption('uid');
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        if ($uid || $username) {
            $field = $uid ? 'uid' : 'username';
            $account = $field == 'uid' ? $uid : $username;
            if ($field == 'uid' && !is_numeric($account))
                return $output->error('UID must be a number');
            $user = UserModel::where($field, $account)->first();
            if (!$user || $user->{$field} != $account)
                return $output->error('The account does not exist.');
            UserModel::reset($user->uid, $password);

            return $output->info('Password reset successfully!');
        }
        else {
            $field = $output->choice($input, 'What is the account type?', ['uid', 'username'], 'uid');
            $account = $output->ask($input, 'What is the account?');
            if (!$account)
                return $output->error("Please enter the account");
            if ($field == 'uid' && !is_numeric($account))
                return $output->error('UID must be a number');
            $password = $output->askHidden($input, 'What is the password?');
            if (!$password)
                return $output->error('Please enter password.');
            $user = UserModel::where($field, $account)->first();
            if (!$user)
                return $output->error('The account does not exist.');
            if ($output->confirm($input, "Do you confirm to reset [{$user->uid}]{$user->username}({$user->nickname})'s password?", true)) {
                UserModel::reset($user->uid, $password);

                return $output->info('Password reset successfully!');
            }
            else
                return $output->warning('To abandon this reset.');
        }
    }
}
