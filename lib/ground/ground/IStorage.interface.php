<?php

interface ground_IStorage {
	function run_query($query, $response);
	function run_update($update, $response);
}
