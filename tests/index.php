<?php 
	$test_json = <<<TEST_JSON
	{
		"id":"xyz",
		"title":"boogers",
		"_r":[
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
	TEST_JSON;

	//test
	//  print_r(serialize(json_decode($test_json, true)));

	//print_r(json_decode($test_json, true));


	//print_r(unserialize(serialize(json_decode($test_json, true))));

	//echo json_encode(unserialize(serialize(json_decode($test_json, true))));