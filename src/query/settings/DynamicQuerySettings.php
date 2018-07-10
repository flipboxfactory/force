<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\force\query\settings;

use Craft;
use flipbox\ember\models\Model;
use flipbox\force\Force;
use flipbox\force\query\DynamicQueryBuilder;
use flipbox\force\query\QueryBuilderInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property Force $module
 */
class DynamicQuerySettings extends Model implements QuerySettingsInterface
{
    /**
     * @var DynamicQueryBuilder
     */
    private $builder;

    /**
     * @inheritdoc
     */
    public function __construct(DynamicQueryBuilder $query, $config = [])
    {
        $this->builder = $query;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     * @return DynamicQueryBuilder
     */
    public function getBuilder(): QueryBuilderInterface
    {
        return $this->builder;
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function inputHtml(): string
    {
        return Craft::$app->getView()->renderTemplate(
            'force/_components/queries/dynamic',
            [
                'settings' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    'builder',
                    'validateBuilder'
                ]
            ]
        );
    }

    /**
     * Query Builder Validation
     */
    public function validateBuilder()
    {
        $builder = $this->getBuilder();

        if (empty($builder->soql)) {
            $this->addError('soql', 'SOQL cannot be empty');
        }
    }
}
