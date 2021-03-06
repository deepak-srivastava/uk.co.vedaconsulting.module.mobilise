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
<div class="crm-block crm-form-block crm-mobilise-group-alumni-block">
{include file="CRM/common/WizardHeader.tpl"}

  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>

  <div class="crm-accordion-wrapper crm-accordion_title-accordion crm-accordion-open">
    <div class="crm-accordion-header">
      <div class="icon crm-accordion-pointer"></div> 
      {ts}Assignment Details{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">

      <table class="form-layout-compressed">
	{if $event}
	  <tr class="crm-mobilise-group-alumni-block-event"><td class="label">{ts}Event{/ts}</td><td>{$event}</td></tr>
	{/if}
	{if $participantSection}
	  {assign var="sthToDisplay" value=1}
	  {if array_key_exists('role', $participant_fields)}
	    <tr class="crm-mobilise-group-alumni-block-role_id"><td class="label">{$form.role_id.label}</td><td>{$form.role_id.html}</td></tr>
	  {/if}
	  {if in_array('status', $participant_fields)}
	    <tr class="crm-mobilise-group-alumni-block-status_id">
	      <td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td>
	    </tr>
	  {/if}
	{/if}
	{if $studentSection}
	  {if array_key_exists('student_contact', $participant_fields)}
	    {assign var="sthToDisplay" value=1}
            {include file="CRM/Mobilise/Override/NewContact.tpl" blockNo=1}
	  {/if}
	{/if}
      </table>
      {if !$sthToDisplay}
        <div class="messages status">{ts}Nothing to update.{/ts}</div>
      {/if}

    </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->

  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>
</div>
