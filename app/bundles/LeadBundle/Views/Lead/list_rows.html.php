<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
        <?php foreach ($items as $item): ?>
            <?php /** @var \Mautic\LeadBundle\Entity\Lead $item */ ?>
            <?php $fields = $item->getFields(); ?>
            <tr<?php if (!empty($highlight)) echo ' class="warning"'; ?>>
                <td>
                    <?php
                    $hasEditAccess = $security->hasEntityAccess(
                        $permissions['lead:leads:editown'],
                        $permissions['lead:leads:editother'],
                        $item->getOwner()
                    );

                    $custom = array();
                    if ($hasEditAccess && !empty($currentList)) {
                        //this lead was manually added to a list so give an option to remove them
                        $custom[] = array(
                            'attr' => array(
                                'href' => $view['router']->generate('mautic_leadlist_action', array(
                                    "objectAction" => "removeLead",
                                    "objectId" => $currentList['id'],
                                    "leadId"   => $item->getId()
                                )),
                                'data-toggle' => "ajax",
                                'data-method' => 'POST'
                            ),
                            'btnText' => 'mautic.lead.lead.remove.fromlist',
                            'iconClass'  => 'fa fa-remove'
                        );
                    }

                    if (!empty($fields['core']['email']['value'])) {
                        $custom[] = array(
                            'attr'      => array(
                                'data-toggle' => 'ajaxmodal',
                                'data-target' => '#MauticSharedModal',
                                'data-header' => $view['translator']->trans('mautic.lead.email.send_email.header', array('%email%' => $fields['core']['email']['value'])),
                                'href'        => $view['router']->generate('mautic_lead_action', array('objectId' => $item->getId(), 'objectAction' => 'email', 'list' => 1))
                            ),
                            'btnText'   => 'mautic.lead.email.send_email',
                            'iconClass'    => 'fa fa-send'
                        );
                    }

                    echo $view->render('MauticCoreBundle:Helper:list_actions.html.php', array(
                        'item'      => $item,
                        'templateButtons' => array(
                            'edit'      => $hasEditAccess,
                            'delete'    => $security->hasEntityAccess($permissions['lead:leads:deleteown'], $permissions['lead:leads:deleteother'], $item->getOwner()),
                        ),
                        'routeBase' => 'lead',
                        'langVar'   => 'lead.lead',
                        'customButtons' => $custom
                    ));
                    ?>
                </td>
                <td>
                    <a href="<?php echo $view['router']->generate('mautic_lead_action', array("objectAction" => "view", "objectId" => $item->getId())); ?>" data-toggle="ajax">
                        <?php if (in_array($item->getId(), $noContactList)) : ?>
                            <div class="pull-right label label-danger"><i class="fa fa-ban"> </i></div>
                        <?php endif; ?>
                        <div><?php echo ($item->isAnonymous()) ? $view['translator']->trans($item->getPrimaryIdentifier()) : $item->getPrimaryIdentifier(); ?></div>
                        <div class="small"><?php echo $item->getSecondaryIdentifier(); ?></div>
                    </a>
                </td>
                <td class="visible-md visible-lg"><?php echo $fields['core']['email']['value']; ?></td>
                <td class="visible-md visible-lg">
                    <?php
                    $flag = (!empty($fields['core']['country'])) ? $view['assets']->getCountryFlag($fields['core']['country']['value']) : '';
                    if (!empty($flag)):
                    ?>
                    <img src="<?php echo $flag; ?>" style="max-height: 24px;" class="mr-sm" />
                    <?php
                    endif;
                    $location = array();
                    if (!empty($fields['core']['city']['value'])):
                        $location[] = $fields['core']['city']['value'];
                    endif;
                    if (!empty($fields['core']['state']['value'])):
                        $location[] = $fields['core']['state']['value'];
                    elseif (!empty($fields['core']['country']['value'])):
                        $location[] = $fields['core']['country']['value'];
                    endif;
                    echo implode(', ', $location);
                    ?>
                    <div class="clearfix"></div>
                </td>
                <td class="text-center">
                    <?php
                    $color = $item->getColor();
                    $style = !empty($color) ? ' style="background-color: ' . $color . ';"' : '';
                    ?>
                    <span class="label label-default"<?php echo $style; ?>><?php echo $item->getPoints(); ?></span>
                </td>
                <td class="visible-md visible-lg">
                    <abbr title="<?php echo $view['date']->toFull($item->getLastActive()); ?>">
                        <?php echo $view['date']->toText($item->getLastActive()); ?>
                    </abbr>
                </td>
                <td class="visible-md visible-lg"><?php echo $item->getId(); ?></td>
            </tr>
        <?php endforeach; ?>
