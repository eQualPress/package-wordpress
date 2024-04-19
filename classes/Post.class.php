<?php

namespace wordpress/classes;

class Post extends \equal\orm\Model {
	public static function getColumns(): array {
		return [
			"name"          => [
				"type"        => "string",
				"description" => "The title of the post",
				"alias"       => "title",
				"required"    => true,
				"unique"      => true
			],
			"title"         => [
				"type"        => "string",
				"description" => "The title of the post",
				"required"    => true,
				"unique"      => true
			],
			"content"       => [
				"type"        => "string",
				"description" => "The content of the post",
				"required"    => true
			],
			"post_id"       => [
				"type"        => "integer",
				"description" => "The ID of the post",
				"required"    => true,
				"unique"      => true
			],
			"post_guid"     => [
				"type"        => "string",
				"description" => "The GUID of the post",
				"required"    => false,
				"unique"      => true
			],
			"status"        => [
				"type"        => "string",
				"selection"   => [
					"publish",
					"pending",
					"draft",
					"auto-draft",
					"future",
					"private",
					"inherit",
					"trash"
				],
				"description" => "The status of the post",
				"required"    => true
			],
			"feature_image" => [
				"type"        => "string",
				"description" => "The path of the post feature image",
				"required"    => false
			],
			"post_type"     => [
				"type"        => "string",
				"selection"   => [
					"post",
					"page",
					"attachment",
					"revision",
					"nav_menu_item",
					"wp_template",
					"wp_template_part"
				],
				"description" => "The type of the post",
				"required"    => true,
			],
			"author"        => [
				"type"        => "string",
				"description" => "The author name of the post",
				"required"    => true
			],
			"author_id"     => [
				"type"        => "integer",
				"description" => "The ID of the author of the post",
				"required"    => true
			],
			"created_at"    => [
				"type"        => "datetime",
				"description" => "The date and time the post was created",
				"required"    => true
			],
			"updated_at"    => [
				"type"        => "datetime",
				"description" => "The date and time the post was last updated",
				"required"    => true
			]
		];
	}
}
