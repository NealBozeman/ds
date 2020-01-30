#Advanced Data Store
- Use complex, structured data, stored in an efficient "wide table" like format
- Hooks for WP to extend "posts" as complex data types

How is data stored? 
-----
```
foo|sdada|sadasd|neal.test.all.day|blah=14$ 0,0,1
foo|sdada|sadasd|neal.test.all.day.blah=14$ 0,0,1
```

How is data accessed?
----
Using greater/less than indexed table scans on expected values

How to use
-----
- Indexing needed on wp_postmeta: meta_key, meta_value, post_id

Test Data
----
```
{
		"id":"xyz",
		"title":"boogers",
		"trs":[
		{
			"id":"sub1",
			"name":"neal recurse",
			"values":[
				"1",
				"2",
				{
					"three":{
						"four": { "five": "six" },
					"thirdobject":"n\$ea.|l",
					"niceone":"bi=ll",
					"complexity":[
						"grapes",
						"apples",
						"pears"
					]
					}
				}
			]
		}
		]
	}
```