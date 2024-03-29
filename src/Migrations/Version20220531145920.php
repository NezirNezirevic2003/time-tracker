<?php

declare(strict_types=1);

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @version 1.20.2
 */
final class Version20220531145920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add invoice text columns to project and activity';
    }

    public function up(Schema $schema): void
    {
        $activities = $schema->getTable('kimai2_activities');
        $activities->addColumn('invoice_text', 'text', ['notnull' => false, 'default' => null]);

        $projects = $schema->getTable('kimai2_projects');
        $projects->addColumn('invoice_text', 'text', ['notnull' => false, 'default' => null]);
    }

    public function down(Schema $schema): void
    {
        $activities = $schema->getTable('kimai2_activities');
        $activities->dropColumn('invoice_text');

        $projects = $schema->getTable('kimai2_projects');
        $projects->dropColumn('invoice_text');
    }
}
