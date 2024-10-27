<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$GLOBALS['aiit_version'] = '1.0.8';
$GLOBALS['aiit_dev_mode'] = 'false';
$GLOBALS['aiit_prompt_settings']    = array(
    'alt1'          => 'Provide a 150 characters long ALT description of this image, suitable for ',
    'alt2'          => ' with the target audience of ',
    'alt3'          => ". In the alt text include relevant keywords for SEO. Don't including symbols '@', '#', '&', '!', quotation marks. Main categories of the site are ",
    'alt4'          => ' - image can be in one of these. Use no quoation marks! No embellished text. Write in ',
    'title1'         => "Provide a succinct summary of the input text in ",
    'title2'         => ", limited to 55 characters, excluding '@', '#', '&', '!', and quotation marks. Generate 3 versions and provide only the shortest one. Input Text",
    'title_2_1'        => "Summarize this text in 6-8 words logical sentence (Don't including symbols '@', '#', '&', '!', quotation marks) in ",
    'caption'       => "Provide a 100 characters long caption for an image based on the ALT text without including symbols '@', '#', '&', quotation mark and without including a period at the end. The caption should invite viewers for a closer look without being too flowery. Be professional! Avoid being repetative. Write in ",
    'description1'  => 'Create a tailored 240-character description for an image based on the provided ALT text. Image is published on ',
    'description2'  => ' with the target audience of ',
    'description3'  => " . The description should provide additional context, focusing on the image's key features and its relevance. Make it informative. Avoid being repetative. NO embellished text. NO quatation marks, hashtags or emojis! Write in ",
);
?>