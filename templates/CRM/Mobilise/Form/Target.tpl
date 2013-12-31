{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
<div class="crm-block crm-form-block crm-mobilise-group-target-block">
{include file="CRM/common/WizardHeader.tpl"}

  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>

  <div class="crm-accordion-wrapper crm-accordion_title-accordion crm-accordion-open">
    <div class="crm-accordion-header">
      <div class="icon crm-accordion-pointer"></div> 
      {ts}Participant Fields{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">

      <table class="form-layout-compressed">
	{if in_array('activity_type', $activity_fields)}
	<tr class="crm-mobilise-group-target-block-activity_type"><td class="label">{ts}Activity Type{/ts}</td><td>{$activityType}</td></tr>
	{/if}
	{if in_array('activity_date', $activity_fields)}
	<tr class="crm-mobilise-group-target-block-activity_date_time">
	  <td class="label">{$form.activity_date_time.label}</td>
	  {if $hideCalendar neq true}
	  <td class="view-value">{include file="CRM/common/jcalendar.tpl" elementName=activity_date_time}</td>
	  {else}
	  <td class="view-value">{$form.activity_date_time.html|crmDate}</td>
	  {/if}
	</tr>
	{/if}
	{if in_array('status', $activity_fields)}
	<tr class="crm-mobilise-group-target-block-status_id">
	  <td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td>
	</tr>
	{/if}
      </table>

    </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->

  <div class="crm-accordion-wrapper crm-accordion_title-accordion crm-accordion-open">
    <div class="crm-accordion-header">
      <div class="icon crm-accordion-pointer"></div> 
      {ts}Selected Contacts{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">
      
    </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>
</div>
