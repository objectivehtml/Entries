## assigned_to_member()
	
This method allows you store a delimited string of member_id's in a channel field that can be used to grab "assigned" entries. An assigned entry conists of any entry authored by the given user, or any entry in which the defined field contains a member_id.

*The string delimeter must be a '|' character.*
	
### PARAMETERS

from_field : This is the channel field that stores the delimeted member_id's. If the defined member_id is stored in this field, the entry is returned in the results.

from_channel : This is the channel that can be defined that contains the field that stores the delimeter member_id's. If no channel is defined, all channels are included in the search.

from_author_id : The default value is the member that is logged in, but use this parameter if you wish to override.

*All parameters from the exp:channel:entries are accepted.*

### Related Tags

- assigned_to_me();
- ids_assigned_to_member();
- ids_assigned_to_me();
	
---

## assigned_to_me()
	
This method returns the same data as `assigned_to_member()`, but rather than specifying an author_id, it only grabs the entries associated with the member that is currectly logged in.

*The string delimeter must be a '|' character.*
	
### PARAMETERS

field : This is the channel field that stores the delimeted member_id's. If the defined member_id is stored in this field, the entry is returned in the results.

*All parameters from the exp:channel:entries are accepted.*

### Related Tags

- assigned_to_member();
- ids_assigned_to_member();
- ids_assigned_to_me();
	
---

## ids_assigned_to_member()
	
This method runs the same query as `assigned_to_member()` but rather than parsing tagdata, it returned a delimited string of entry_id's. The string will be delimited with a '|' character.

### PARAMETERS

from_field : This is the channel field that stores the delimeted member_id's. If the defined member_id is stored in this field, the entry is returned in the results.

from_channel : This is the channel that can be defined that contains the field that stores the delimeter member_id's. If no channel is defined, all channels are included in the search.

from_author_id : The default value is the member that is logged in, but use this parameter if you wish to override.

### Related Tags

- ids_assigned_to_me();
- assigned_to_member
- assigned_to_me();
	
---

## ids_assigned_to_me()
	
This method runs the same query as `assigned_to_me()` but rather than parsing tagdata, it returned a delimited string of entry_id's. The string will be delimited with a '|' character.

### PARAMETERS

from_field : This is the channel field that stores the delimeted member_id's. If the defined member_id is stored in this field, the entry is returned in the results.

from_channel : This is the channel that can be defined that contains the field that stores the delimeter member_id's. If no channel is defined, all channels are included in the search.

*All parameters from the exp:channel:entries are accepted.*

### Related Tags

- ids_assigned_to_member();
- assigned_to_member
- assigned_to_me();

---

## get()
	
This method is simply an alias to exp:channel:entries. This big difference here is you can actually nest your tags with unique prefixes so everything parses correctly.

### PARAMETERS

*All parameters from the exp:channel:entries are accepted.*

### EXAMPLE

	&#123;exp:entries:get
		channel="channel_1"
	}		
		{entry_id}
		{title}
		{some_custom_field}
		
		&#123;exp:entries:get
			channel="channel_2"
			prefix="2:"
			entry_id="{some_custom_field}"
		}
	
			{2:entry_id}
			{2:title}
			{2:some_custom_field}
					
			&#123;exp:entries:get
				channel="channel_3"
				prefix="3:"
				entry_id="{2:some_custom_field}"
			}
				
				{3:entry_id}
				{3:title}
				{3:some_custom_field}
						
			{/exp:entries:get}
		
		{/exp:entries:get}
	
	{/exp:entries:get}
	

*Numbered prefixes are used to represent nested depth, you can use whatever you prefer. It's also importan to be mindful of performance. Test your code thoroughly when using nested tags.*

---

## by_category()
	
This method makes working with categories easier. You can grab entries associated to category id, category name, category url title, and parent category id.

### PARAMETERS

category_id : 
Specify one or category id's using a '|' delimiter.

category_name :
Specify one or category name's using a '|' delimiter.

category_url_title :
Specify one or category url title's using a '|' delimiter.

category_parent_id :
Specify one or category parent id's using a '|' delimiter.

*All parameters from the exp:channel:entries are accepted.*

---

## profile()
	
This method assumes a member profile is stored as a channel entry, and by default it assumes that channel name is "members". This tag really isn't much different than the exp:entries:get tag other than it makes pulling profile information more convenient. This method defaults to the member that is currently logged in.

### PARAMETERS

*All parameters from the exp:channel:entries are accepted.*
		
---

## my_category_entries()
	
This method uses the exp:entries:profile tag as a base, but rather than return the profile data, it returned all the entries with the same categories assigned to the profile entry. This is a way to easily use categories to related things across multiple channels.

### PARAMETERS

*All parameters from the exp:channel:entries are accepted.*

---

## my_category_ids()
	
This method uses the exp:entries:profile tag as a base, but rather than parse tagdata, it returns associated category id's to the returned profile entry.

### PARAMETERS

*All parameters from the exp:channel:entries are accepted.*
		
---

## related()
	
This returned the related entries to any specified entry_id.

### PARAMETERS

rel_entry_id : 
This is the entry from which you wish to use to grab other related entries. So if you specific rel_entry_id 1, it will grab all the entries related to entry_id 1.

*All parameters from the exp:channel:entries are accepted.*
		
---

## reverse_related()
	
This method is the opposite of exp:entries:related. This returned all the reverse_relationships from any given entry_id.

### PARAMETERS

*All parameters from the exp:channel:entries are accepted.*
	