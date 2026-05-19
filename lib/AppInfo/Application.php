<?php
/**
 * Nextcloud - user_sql
 *
 * @copyright 2018 Marcin Łojewski <dev@mlojewski.me>
 * @author    Marcin Łojewski <dev@mlojewski.me>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace OCA\UserSQL\AppInfo;

use OCA\UserSQL\Backend\GroupBackend;
use OCA\UserSQL\Backend\UserBackend;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\IGroupManager;
use OCP\IUserManager;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * The application bootstrap class.
 *
 * @author Marcin Łojewski <dev@mlojewski.me>
 */
class Application extends App implements IBootstrap
{
    public const APP_ID = "user_sql";

    public function __construct(array $urlParams = [])
    {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void
    {
    }

    public function boot(IBootContext $context): void
    {
        try {
            $appContainer = $context->getAppContainer();
            $serverContainer = $context->getServerContainer();

            $userBackend = $appContainer->get(UserBackend::class);
            if ($userBackend->isConfigured()) {
                $serverContainer->get(IUserManager::class)
                    ->registerBackend($userBackend);
            }

            $groupBackend = $appContainer->get(GroupBackend::class);
            if ($groupBackend->isConfigured()) {
                $serverContainer->get(IGroupManager::class)
                    ->addBackend($groupBackend);
            }
        } catch (Throwable $exception) {
            $context->getServerContainer()
                ->get(LoggerInterface::class)
                ->error(
                    "Failed to register user_sql backends: "
                    . $exception->getMessage(),
                    ["app" => self::APP_ID, "exception" => $exception]
                );
        }
    }
}
