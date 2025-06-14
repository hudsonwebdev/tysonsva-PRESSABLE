<?php


add_action( 'rest_api_init', 'neighborhood_json_route' );

function neighborhood_json_route() {
    register_rest_route( 'neighborhood', 'geojson', array(
                    'methods' => 'GET',
                    'callback' => 'neighborhood_geojson',
                    'permission_callback' => '__return_true'
                )
            );
}

function neighborhood_geojson() {



    $args = array(
        'post_type' => 'neighborhood',
        'posts_per_page' => -1

    );

    $neighborhoods = get_posts($args);


    $featurecollection = array("type" => "FeatureCollection","features"=>array());

    $featurecollection['features'] = array();


    foreach($neighborhoods as $hood){

        $feature = [];

        $area_geojson = get_field('area_geojson',$hood->ID);

        $color = get_field('color',$hood->ID);

        if($area_geojson){

          $geojson = json_decode($area_geojson,true);

          if($geojson){


             $feature['coords'] = $geojson;

             $feature['properties']['id']  = $hood->ID;
            
             $feature['properties']['title']  = $hood->post_title;

             $feature['properties']['color']  = $color;

             $feature['properties']['slug']  = $hood->post_name;

             array_push($featurecollection["features"],$feature);

          }

          


        }
        
    }


    return json_encode($featurecollection);
}