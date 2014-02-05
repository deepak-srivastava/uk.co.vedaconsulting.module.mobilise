Some queries: 

Q1 - it seems that, in some instances, the role is being prepopulated on type? It'd be useful if we knew how to do this for different roles. Is this done by jQuery or by code, or just by civievent?

A1. Following field values are responsible for this:
- $_metadata['Careers']['participant_fields']['role'] 
- $_metadata['Mentor']['participant_fields']['role'] 
- $_metadata['Work Experience']['participant_fields']['role']
- $_metadata['Non Careers']['participant_fields']['role'] 

Q2 - In the same vein as Q1, there are role options (i.e. volunteer) which probably aren't needed at all, and others that aren't needed for the teacher interface, but are likely to be needed for the back end system (i.e. alumni outreach officer, facilitator). Is there a jQuery or PHP function that modifies these anyway? If so, where?

A2. For those aren't needed at all can be disabled from 
Administer >>  CiviEvent >> Participant Roles

For those which aren't needed for teacher interface could be disabled / made-hidden from jQuery code, which can be added to 
uk.co.vedaconsulting.module.mobilise/templates/CRM/Mobilise/Form/Participant.tpl

Q3 - We'd like to rename 'Mentor (Alumni)' -> 'Mentor'. How?

A3. 
In file CRM/Mobilise/Form/Mobilise.php set $_metadata['Mentor']['title'] to "Mentor" (from 'Mentor (Alumni)').

On Administer >>  CiviEvent >> Participant Roles
Rename participant role from "Mentor (Alumni)" to "Mentor".

In file CRM/Mobilise/Form/Mobilise.php, adjust following accordingly: 
- $_metadata['Careers']['participant_fields']['role'] 
- $_metadata['Mentor']['participant_fields']['role'] 
- $_metadata['Work Experience']['participant_fields']['role']
- $_metadata['Non Careers']['participant_fields']['role'] 

Q4 - We'd like to customise the participant status options. Expired isn't relevant to us, and we'd like "attended" to be renamed "completed" and appear at the top of the list. How can we do this?

A4. Status can be configured/renamed from
Administer >>  CiviEvent >> Participant Statuses

Order of list is decided based on weights of status on above screen, which is also configurable.

Q5 - After a discussion, it was decided that, contrary to the initial spec, we'd like "non-careers event" to not be in the list. However, disabling it in the CiviEvent options didn't achieve this. How can we stop them from appearing in the list?

A5. $_metadata['Non Careers'] would need to be removed / commented.

Q6 - Further to Q5, how can we change the labels on these options?

A6. $_metadata has "title" field which will need to be adjusted.
Example:
- $_metadata['Careers']['title']
- $_metadata['Mentor']['title']
- $_metadata['Donation']['title']
- .... 
