<?php
/*
 Template Name: Explore Tysons

*/

get_header();

?>
<style>
	.top-banner {
		font-family: "silkabold", "Arial", "Helvetica", sans-serif;
	}
	.explore-head {
		position: relative;
		padding: 0;
	}

	.explore-head .uk-container {
		position: absolute;
		margin: auto;
		top: 30%;
		left: 0;
		right: 0;
	}

	/* main section:nth-child(odd) {
		border-bottom: 2px solid #c00;
	} */


	main section {
		padding: 60px 0;
	}

	h2.script {
		color: #385dff;
		font-size: 75px;
		font-family: "silkabold", "Arial", "Helvetica", sans-serif;
		line-height: 1.25;
	}

	h2.h2ltblu {
		color: #385dff;
		font-weight: normal;
	}

	h3.h3blu {
		color: #0c0042;
	}

	.c-txt {
		text-align: center;
	}

	.tca-button {
		font-size: 13px;
		padding: 5px 10px;
		line-height: 26px;
	}

	.gform-body .tca-button {
		font-size: auto !important;
		padding: auto !important;
		line-height:  auto !important;
	}

	.tca-button-wrap.blue .tca-button:before,
	.tca-button-wrap.blue .tca-button:after {
		content: none;

	}

	.explore-head h1 {
		color: #fff;
		font-size: 8vw;
		line-height: 0.8;
		font-family: "silkabold", "Arial", "Helvetica", sans-serif;
		letter-spacing: -0.03em;
		color: #385dff;

	}

	.explore-head h1 span {
		display: block;
		font-size: 4vw;
		margin-left: 1vw;
		letter-spacing: 0;
		color: #fff;

	}

	.explore-head span.wbr {
		text-transform: uppercase;
		font-size: 0.2em;
		margin-left: 7em;
	}


	.exlogo {
		position: absolute;
		left: 30%;
		transform: translate(-50%, -50%);
		width: 300px;
		height: auto;
		max-width: 50%;
		top: 50%;
	}


	@media(min-width:768px) {

		.exlogo {
			left: 5%;
			top: 65%;
			transform: translateY(-90%);
			width: 50%;
		}
	}

	@media(min-width:1200px) {
		.exlogo {
			width: 700px;
		}
	}

	@media(min-width:1960px) {
		.exlogo {
			width: 50%;
		}
	}

	.diptych .item {
        margin: 30px;
        padding-left: 150px;
    }

	.diptych .item.circulator,
	.diptych .item.metrorail {
		background-repeat: no-repeat;
		background-position: left top;
		background-size: 130px;
	}

	.diptych .item.circulator {
		background-image: url('<?php echo get_template_directory_uri() ?>/expimg/explore/bus-icon.png');
	}

	.diptych .item.metrorail {
		background-image: url('<?php echo get_template_directory_uri() ?>/expimg/explore/rail-icon.png');
	}

	.diptych h3 {
		font: 900 45px Roboto, sans-serif;
	}

	.diptych p {
		font: 400 21px Roboto, sans-serif;
	}

	

	.triptych .item {
		position: relative;
		margin: 70px 0 30px;
		padding: 80px 0 0;
		background: #c5eafa;
		display: flex;
		flex-direction: column;
		text-align: center;
	}

	.triptych .item h3 {
		color: #385dff;
		font-size: 52px;
		margin: 0;
	}

	.triptych p {
		padding: 0 20px;
	}

	.triptych figure {
		margin: auto 0 0 0;
		position: relative;
	}

	.triptych .item figure:after {
		content: "";
		display: block;
		box-shadow: inset 0 40px 30px -10px rgba(197, 235, 250, 1);
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		height: 100%;
		width: 100%;
	}



	.triptych .item:before {
		position: absolute;
		margin: auto;
		top: -80px;
		left: 0;
		right: 0;
		width: 150px;
	}

	.triptych .item.bike:before {
	content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='152' height='152'%3E%3Cdefs%3E%3CclipPath id='b'%3E%3Cpath data-name='Path 20' d='M0 54.672h73.37v-77.219H0Z' transform='translate(0 22.547)' fill='none'/%3E%3C/clipPath%3E%3Cfilter id='a' x='0' y='0' width='152' height='152' filterUnits='userSpaceOnUse'%3E%3CfeOffset dx='3' dy='3'/%3E%3CfeGaussianBlur stdDeviation='5' result='blur'/%3E%3CfeFlood flood-opacity='.161'/%3E%3CfeComposite operator='in' in2='blur'/%3E%3CfeComposite in='SourceGraphic'/%3E%3C/filter%3E%3C/defs%3E%3Cg data-name='Group 26'%3E%3Cg transform='translate(0 -.005)' filter='url(%23a)'%3E%3Cg data-name='Ellipse 5' transform='translate(22 22)' fill='%2319a374' stroke='%23fff' stroke-width='10'%3E%3Ccircle cx='51' cy='51' r='51' stroke='none'/%3E%3Ccircle cx='51' cy='51' r='56' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg data-name='Group 35'%3E%3Cg data-name='Group 34' transform='translate(37.315 29.39)' clip-path='url(%23b)'%3E%3Cg data-name='Group 30'%3E%3Cpath data-name='Path 16' d='M60.066 49.094a12.371 12.371 0 0 0-1.64.123L54.15 32.165a7.286 7.286 0 0 1-3.12 2.5l1.22 4.812-4.959.38a9.827 9.827 0 0 1 .144 3.251l-.065.572 2.449-.188-2.826 3.362-.791 6.685 7.377-8.767 1.363 5.415a14.081 14.081 0 0 0-8.182 12.969c0 7.761 5.963 14.062 13.305 14.062 7.326 0 13.305-6.3 13.305-14.062s-5.979-14.062-13.304-14.062m0 24.306a9.988 9.988 0 0 1-9.693-10.244 10.293 10.293 0 0 1 5.5-9.22l1.428 5.678a4.7 4.7 0 0 0-1.593 3.541 4.481 4.481 0 0 0 4.353 4.6 4.615 4.615 0 0 0 .723-9.117l-1.411-5.7c.229-.017.442-.034.688-.034 5.339 0 9.692 4.6 9.692 10.244S65.405 73.4 60.066 73.4' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 31'%3E%3Cpath data-name='Path 17' d='m30.649 58.346.706-5.922-3.5-5.747-5.421-.726a1.791 1.791 0 0 1-.411-.089l-2.919 4.655a12.612 12.612 0 0 0-5.8-1.425c-7.326 0-13.305 6.3-13.305 14.062S5.979 77.22 13.304 77.22c6.716 0 12.285-5.3 13.172-12.155h4.795a8.287 8.287 0 0 1-.918-3.819h-3.876a14.322 14.322 0 0 0-4.37-8.613l2.6-4.1ZM13.305 73.399c-5.339 0-9.692-4.6-9.692-10.244s4.353-10.247 9.692-10.247a9.159 9.159 0 0 1 3.778.819l-3.089 4.897a4.687 4.687 0 0 0-.688-.068 4.489 4.489 0 0 0-4.357 4.599 4.493 4.493 0 0 0 4.353 4.6 4.369 4.369 0 0 0 3.959-2.692h5.565a9.869 9.869 0 0 1-9.524 8.333m9.524-12.151h-5.561a4.971 4.971 0 0 0-.264-.5l3.089-4.9a10.486 10.486 0 0 1 2.74 5.4' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 32'%3E%3Cpath data-name='Path 18' d='m39.083 36.158-6.655-.877 2.716-8.137 4.411 3.01a3.321 3.321 0 0 0 1.428.548c.051.007.1.01.154.014l7.124.6a3.474 3.474 0 0 0 3.668-3.308 3.566 3.566 0 0 0-3.13-3.88l-6.267-.524s-6.63-4.843-7.48-5.175c-.956-.377-4.87-1.832-4.87-1.832a5.161 5.161 0 0 0-4.185.25 5.625 5.625 0 0 0-2.771 3.317l-4.723 14.611a5.865 5.865 0 0 0 .524 4.8 5.267 5.267 0 0 0 3.849 2.6l11.747 1.545a1.446 1.446 0 0 1 1.2 1.63L34 60.806a4 4 0 0 0 3.322 4.507 3.87 3.87 0 0 0 4.267-3.507l2.257-19.179a5.743 5.743 0 0 0-4.764-6.469' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 33'%3E%3Cpath data-name='Path 19' d='M32.749 15.376a7.363 7.363 0 0 0 8.87-5.787A7.819 7.819 0 0 0 36.143.211a7.36 7.36 0 0 0-8.87 5.788 7.819 7.819 0 0 0 5.476 9.377' fill='%23fff'/%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
	}

	.triptych .item.roll:before { 
	content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='152' height='152'%3E%3Cdefs%3E%3CclipPath id='b'%3E%3Cpath data-name='Rectangle 85' fill='none' d='M0 0h57.307v74.266H0z'/%3E%3C/clipPath%3E%3Cfilter id='a' x='0' y='0' width='152' height='152' filterUnits='userSpaceOnUse'%3E%3CfeOffset dx='3' dy='3'/%3E%3CfeGaussianBlur stdDeviation='5' result='blur'/%3E%3CfeFlood flood-opacity='.161'/%3E%3CfeComposite operator='in' in2='blur'/%3E%3CfeComposite in='SourceGraphic'/%3E%3C/filter%3E%3C/defs%3E%3Cg data-name='Group 114'%3E%3Cg filter='url(%23a)'%3E%3Cg data-name='Ellipse 5' transform='translate(22 22)' fill='%2319a374' stroke='%23fff' stroke-width='10'%3E%3Ccircle cx='51' cy='51' r='51' stroke='none'/%3E%3Ccircle cx='51' cy='51' r='56' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg data-name='Group 113'%3E%3Cg data-name='Group 112' transform='translate(45.293 33.687)' clip-path='url(%23b)'%3E%3Cpath data-name='Path 37' d='m51.269 61-6.361-30.16a3.58 3.58 0 0 0-1.5-5.89l-5.788-1.229-7.756-8.187a5.116 5.116 0 0 0-8.477 2.472l-3.168 16.4L13.631 46.2 2.082 51.558a3.595 3.595 0 1 0 3.026 6.522l12.917-5.989a3.6 3.6 0 0 0 1.875-2.054l2.823-6.854 4.406 8.033-.92 12.284a3.575 3.575 0 0 0 .563 2.208H15.109a6.655 6.655 0 1 0 .018 3.735H37.17a1.871 1.871 0 0 0 1.376-.6l8.618-9.36.434 2.226A6.646 6.646 0 1 0 51.269 61M8.735 70.531a2.913 2.913 0 1 1 2.2-4.819h-2.2a1.8675 1.8675 0 1 0 0 3.735H11a2.9 2.9 0 0 1-2.26 1.084m27.616-4.819H32.82a3.6 3.6 0 0 0 .56-1.672l1.013-13.554a3.625 3.625 0 0 0-.625-2.309L27.6 37.6 29.876 26l3.156 3.335a3.626 3.626 0 0 0 1.5.948l6.668 1.505.092.025 4.985 23.123Zm14.3 4.819a2.919 2.919 0 0 1-2.913-2.916 2.885 2.885 0 0 1 .657-1.835l.422 2.158a1.867 1.867 0 0 0 1.835 1.509 1.62 1.62 0 0 0 .36-.034 1.868 1.868 0 0 0 1.475-2.192l-.422-2.149a2.912 2.912 0 0 1-1.413 5.459M29.334 12.846a6.423 6.423 0 1 0-6.423-6.423 6.423 6.423 0 0 0 6.423 6.423' fill='%23fff'/%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
	}

	.triptych .item.walk:before {
	content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='152' height='152'%3E%3Cdefs%3E%3CclipPath id='b'%3E%3Cpath data-name='Path 12' d='M0 56.1h45.347v-79.31H0Z' transform='translate(0 23.207)' fill='none'/%3E%3C/clipPath%3E%3Cfilter id='a' x='0' y='0' width='152' height='152' filterUnits='userSpaceOnUse'%3E%3CfeOffset dx='3' dy='3'/%3E%3CfeGaussianBlur stdDeviation='5' result='blur'/%3E%3CfeFlood flood-opacity='.161'/%3E%3CfeComposite operator='in' in2='blur'/%3E%3CfeComposite in='SourceGraphic'/%3E%3C/filter%3E%3C/defs%3E%3Cg data-name='Group 47'%3E%3Cg transform='translate(0 -.005)' filter='url(%23a)'%3E%3Cg data-name='Ellipse 5' transform='translate(22 22)' fill='%2319a374' stroke='%23fff' stroke-width='10'%3E%3Ccircle cx='51' cy='51' r='51' stroke='none'/%3E%3Ccircle cx='51' cy='51' r='56' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg data-name='Group 21'%3E%3Cg data-name='Group 20' transform='translate(50.467 33.35)' clip-path='url(%23b)'%3E%3Cg data-name='Group 17'%3E%3Cpath data-name='Path 9' d='M5.343 73.429a4.473 4.473 0 0 0 2.392 5.615 4.241 4.241 0 0 0 1.418.267 4.1 4.1 0 0 0 3.828-2.847l5.461-15.806-6.329-6.791Z' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 18'%3E%3Cpath data-name='Path 10' d='m42.33 30.873-7.143-1.152a1.416 1.416 0 0 1-.988-.656c-6.012-9.395-7.231-9.894-9.032-10.02l-5.823-.4c-1.114-.079-2.174-.14-10.857 5.359a11.945 11.945 0 0 0-4.32 4.87L.408 36.536a3.929 3.929 0 0 0 1.49 5.123 3.33 3.33 0 0 0 1.644.437 3.522 3.522 0 0 0 3.145-2.03l3.756-7.665a4.555 4.555 0 0 1 1.649-1.863c.267-.171.506-.318.759-.475l-2.529 11.849a7.309 7.309 0 0 0 1.756 6.547l11.38 12.2a.92.92 0 0 1 .229.379l4.928 15.331a4.117 4.117 0 0 0 3.866 2.943 3.639 3.639 0 0 0 1.326-.246 4.42 4.42 0 0 0 2.519-5.54l-4.928-15.334a9.853 9.853 0 0 0-2.163-3.7l-6.38-6.849 4.632-15.615q.4.625.851 1.323a8.535 8.535 0 0 0 5.786 3.875l7.146 1.152a3.579 3.579 0 0 0 4.033-3.189 3.761 3.761 0 0 0-2.977-4.316' fill='%23fff'/%3E%3C/g%3E%3Cg data-name='Group 19'%3E%3Cpath data-name='Path 11' d='M22.501 16.782a8.01 8.01 0 0 0 8.667-7.457A8.3 8.3 0 0 0 24.203.05a8.012 8.012 0 0 0-8.667 7.454 8.3 8.3 0 0 0 6.965 9.278' fill='%23fff'/%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
	}

	.triptych-brd {
		margin-top: 80px;
	}

	.triptych-brd .item {
		width: 29%;
		text-align: center;
	}

	.triptych-brd .item.wide {
		width: 39%;
		border-left: 1px solid #ccc;
		border-right: 1px solid #ccc;
	}

	.tetraptych h3 {
		color: #0c0042;
		font: 900 38px Roboto, sans-serif;
		text-align: center;
	}


	.tetraptych .item {
		padding: 20px 30px;
		border: 5px solid #385dff
	}


	.tetraptych dl {
		text-align:  left;
	}

	.tetraptych dt {
		color: #04b5df;
		margin-bottom: 10px;
	}

	.tetraptych dd {
		font-size: 18px;
		font-weight: 500;
/* 		margin-left: 1.5em; */
		margin-left: 0;
		margin-bottom: 0.75em;
	}

	.apps {
		display: flex;
		justify-content: space-around;
		flex-wrap: wrap;
	}

	.cstr {
		width: 50%;
		display: flex;
		flex-direction: column;
		text-align: center;
		margin-bottom: 20px;
	}

	.apps img {
		margin: auto auto 10px;
		width: 70px;
	}


	.bg-img {
		width: 100%;
		height: auto;
	}

	
	.col-md-6 {
		width: 48%;
	}

	.outro {
		padding-bottom: 200px;
	}

	
	.grad-bg {
		background: #260d66;
	}

	.grad-bg h2.script {
		color: #fff;
	}

	.grad-bg .item {
		background: #fff;
	}

	.d-flex.flex-wrap {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-evenly;
	}

	.post-card {
		padding: 20px;
		width: 100%;
		display: flex;
		flex-direction: column
	}

	@media (min-width: 768px) {
		.post-card {
			width: 50%
		}
	}

	@media (min-width: 992px) {
		.post-card {
			width: 33.3333333333%
		}
	}

	.post-card .border {
		border: 1px solid #ccc !important;
		height: 100%
	}

	.post-card .border .inner {
		border: 15px solid #fff !important;
		background: #fff;
		height: 100%
	}

	.post-card .border .inner .image-wrap {
		position: relative;
		overflow: hidden;
		height: 200px
	}

	.post-card .border .inner .image-wrap img {
		-o-object-fit: cover !important;
		object-fit: cover !important;
		min-height: 100%;
		min-width: 100%
	}

	.post-card .border .inner .image-wrap .label-wrap {
		position: absolute;
		top: 0;
		left: 0
	}

	.post-card .border .inner .image-wrap .label-wrap .content-label {
		font-size: .6rem
	}

	.post-card .border .inner .category-label {
		font-size: .6rem
	}

	.post-card .border .inner .category-label.blog {
		color: #3537f9
	}

	.post-card .border .inner .card-info {
		padding: 15px
	}

	.post-card .border .inner .card-info .top {
		display: flex;
		width: 100%;
		justify-content: space-between;
		font-weight: 700;
		padding-bottom: 10px;
		color: #969696
	}

	.post-card .border .inner .card-info .top .cats {
		font-size: .6rem
	}

	.post-card .border .inner .card-info .top .date {
		font-size: .6rem;
		text-align: right
	}

	.post-card .border .inner .card-info .top .type {
		font-size: .6rem
	}

	.post-card .border .inner .card-info h3 {
		font-size: 32px;
		line-height: 125%;
		margin: 0.5em 0;
	}

	.post-card .border .inner .card-info h3 a {
		transition: color 300ms;
	}

	.post-card .border .inner .card-info h3 a:hover {
		text-decoration: none;
	}

	.acf-gallery {
		text-align: center;
	}
	
	.acf-gallery img {
		max-width: 12%;
		max-height: 150px;
		width: auto;
		height: auto;
		margin: 25px;
	}

	#gform_wrapper_4 .ginput_container input[type="text"],
	#gform_wrapper_4 .ginput_container input[type="email"] {
		border: 1px solid #385dff!important;
		height: 33px!important;
		width: 100%!important;
		margin-bottom: 1em!important;
	}

	#gform_wrapper_4 label.gfield_label,
	#gform_wrapper_4 .gform-field-label,
	#gform_wrapper_4 p.gform_required_legend,
	#gform_wrapper_4 .gfield--type-html {
		color: #385dff!important;
		font-size: 16px!important;
	}

	#gform_wrapper_4.gform-theme--foundation .gform_fields {
		row-gap: 15px!important;
	}

	@media screen and (min-width: 640px) {
		.tetraptych,
		.triptych,
		.diptych,
		.triptych-brd,
		.signup .row {
			display: flex;
			justify-content: space-between;
		}

		.triptych .item {
			width: 32%;
		}

		.tetraptych .item {
			width: 24%;
		}
	}
</style>
	<main id="primary" class="site-main">
	
			<?php
			while ( have_posts() ) :
				the_post();

				the_content();

			endwhile; // End of the loop.
			?>
			<div class="top-banner" style="text-transform:uppercase;height:70px;padding:10px;text-align:center;background:#e1ec97;line-height:52px;font-size:.8rem;"><a href="#newsletter" style="color:#000">Click Here</a> to signup for a weekly update on the latest events and rewards.</div>
			


			<section class="explore-head">
				<img src="/wp-content/uploads/2025/02/exploreheader.jpg" alt="Explore Tysons" class="bg-img">
				<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/explore_tysons_logo.png" alt="Explore Tysons: Walk Bike Roll" class="exlogo">
				


			</section>

			<section class="choose-path">
				<div class="uk-container full-page section-content">
					<h2 class="script cyp">Choose your path!</h2>
					<h3 class="h3blu">Get around Tysons on foot, bike, skates, scooter, <br> or your preferred set of wheels! </h3>
					<div class="triptych">
						<div class="item walk">
							<h3>Walk</h3>
							<p>Tysons has an extensive network of sidewalks, trails, and paths to help you get to work and back home as well as all those places in between. <a href="/exploretysons/tysonsgetaroundguide">Use the Tysons Get Around Guide to help you find the best routes!</a>
							</p>
							<figure>
								<picture>
									<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/walk-img.jpg" alt="Walk Portriat">
								</picture>
							</figure>
						</div>
						<div class="item bike">
							<h3>Bike</h3>
							<p>Whether you use a personal bike, trike or Capital Bikeshare, Tysons has many bike-friendly destinations and routes for you to explore. Trails and buffered bike lanes are comfortable options but you may consider being on a sidewalk in some locations. <a href="<?php echo site_url(); ?>/explore-tysons/explore-tysons-by-bike/" target="_blank">Click here for Resources, Events, and Information.</a>
							</p>
							<figure>
								<picture>
									<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/bike-img.jpg" alt="Bike Portriat">
								</picture>
							</figure>
						</div>
						<div class="item roll">
							<h3>Roll</h3>
							<p>Scooters, skateboards, hoverboards, Segways… there are so many innovative ways to roll! If you don’t want have time to own and maintain a device, rent a Bird scooter to zip around Tysons! Have questions? <a href=" <?php echo site_url(); ?>/explore-tysons/explore-tysons-by-rolling/" target="_blank"> Click here for Resources, Events, and Information.</a>
							</p>
							<figure>
								<picture>
									<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/roll-img.jpg" alt="Roll Portriat">
								</picture>
							</figure>
						</div>
					</div>
					<h2 class="h2ltblu c-txt">Walk, Bike, or Roll to Public Transit Connections</h2>
					<div class="diptych">
						<div class="item circulator">
							<h3>Tysons Circulator</h3>
							<p>Fairfax Connector Routes <a href="https://www.fairfaxcounty.gov/connector/sites/connector/files/Assets/Documents/PDF/Route%20PDFs/Route_423_062415_web.pdf" target="_blank">423</a> and <a href="https://www.fairfaxcounty.gov/connector/sites/connector/files/Assets/Documents/PDF/ccvt/CCVT%20Phase%201%20June%202024%20Profiles/Route_427_0624.pdf" target="_blank">427</a> connect shopping centers, office buildings, and metro stations–a convenient and sustainable transportation option for traveling within Tysons. </p>
						</div>
						<div class="item metrorail">
							<h3>Metrorail</h3>
							<p>The McLean, Tysons, Greensboro, and Spring Hill Metrorail stations are in Tysons–a convenient and affordable option to get to work and play. <a href="https://www.wmata.com/service/rail/" target="_blank">Learn More.</a>
							</p>
						</div>
					</div>
				</div>
			</section>

			<section class="resources">
				<div class="uk-container full-page section-content">
			  		<h2 class="script" style="margin-bottom: 0;">Resources</h2>
					<h2 class="h2ltblu" style="margin-top: 0;">Plan your Walk, Bike, or Roll trip in Tysons!</h2>
					<div class="triptych-brd">
						<div class="item">
							<h4>Bike and Roll Apps</h4>
							<div class="apps">
								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/capital-bikeshare.png" alt="Capital Bikedshare">
								<div class="tca-button-wrap blue">
									<a href="https://apps.apple.com/us/app/capital-bikeshare/id1233403073" class="ios app tca-button" target="_blank">Download for iPhone</a>
								</div>
								<div class="tca-button-wrap blue">
									<a href="https://play.google.com/store/apps/details?id=com.motivateco.capitalbikeshare&amp;hl=en_US" class="droid app tca-button" target="_blank">Download for Android</a>
								</div>
								</div>
							
								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/bird.png" alt="Bird">
								<div class="tca-button-wrap blue">
									<a href="https://apps.apple.com/us/app/bird-ride-electric/id1260842311" class="ios app tca-button" target="_blank">Download for iPhone</a>
								</div>
								<div class="tca-button-wrap blue">
									<a href="https://play.google.com/store/apps/details?id=co.bird.android&amp;hl=en_US" class="droid app tca-button" target="_blank">Download for Android</a>
								</div>
								</div>
							</div>
						</div>

						<div class="item wide">
							<h4>Trip Planning</h4>
							<div class="apps">
								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/google-maps.png" alt="Google Maps">
								<div class="tca-button-wrap blue">
									<a href="https://maps.google.com" class="ios app tca-button" target="_blank">Open Google Maps</a>
								</div>
								</div>
								
								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/apple-maps.png" alt="Apple Maps">
								<div class="tca-button-wrap blue">
									<a href="https://maps.apple.com" class="droid app tca-button" target="_blank">Open Apple Maps</a>
								</div>
								</div>
								
								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/map-ride-app.webp" alt="Map My Ride">
								<div class="tca-button-wrap blue">
									<a href="https://itunes.apple.com/us/app/map-my-ride-gps-cycling-route/id292223170?mt=8" class="ios app tca-button" target="_blank">Download for iPhone</a>
								</div>
								<div class="tca-button-wrap blue">
									<a href="https://play.google.com/store/apps/details?id=com.mapmyride.android2&amp;hl=en" class="droid app tca-button" target="_blank">Download for Android</a>
								</div>
								</div>
								
								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/tripgo.png" style="width: 116px; margin: 23px auto;" alt="TRIPGO">
								<div class="tca-button-wrap blue">
									<a href="https://apps.apple.com/au/app/tripgo/id533630842" class="ios app tca-button" target="_blank">Download for iPhone</a>
								</div>
								<div class="tca-button-wrap blue">
									<a href="https://play.google.com/store/apps/details?id=com.buzzhives.android.tripplanner" class="droid app tca-button" target="_blank">Download for Android</a>
								</div>
								</div>
								
								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/citymapper.png" style="width: 116px; margin-top: 20px;" alt="Citymapper">
								<div class="tca-button-wrap blue">
									<a href="https://apps.apple.com/us/app/citymapper-all-live-transit/id469463298" class="ios app tca-button" target="_blank">Download for iPhone</a>
								</div>
								<div class="tca-button-wrap blue">
									<a href="https://play.google.com/store/apps/details?id=com.citymapper.app.release" class="droid app tca-button" target="_blank">Download for Android</a>
								</div>
								</div>
								
								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/transit-app.png" alt="Transit">
								<div class="tca-button-wrap blue">
									<a href="https://apps.apple.com/us/app/transit-subway-bus-times/id498151501" class="ios app tca-button" target="_blank">Download for iPhone</a>
								</div>
								<div class="tca-button-wrap blue">
									<a href="https://play.google.com/store/apps/details?id=com.thetransitapp.droid&amp;hl=en_US" class="droid app tca-button" target="_blank">Download for Android</a>
								</div>
								</div>
							</div>
						</div>
						<div class="item">
							<h4>Public Transit</h4>
							<div class="apps">
								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/metro.png" alt="Metro">
								<div class="tca-button-wrap blue">
									<a href="https://apps.apple.com/us/app/id1516539463" class="ios app tca-button" target="_blank">Download for iPhone</a>
								</div>
								<div class="tca-button-wrap blue">
									<a href="https://play.google.com/store/apps/details?id=com.wmata.smartrip&amp;utm_source=wmatacom&amp;utm_campaign=androidlaunch&amp;pcampaignid=pcampaignidMKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1" class="droid app tca-button" target="_blank">Download for Android</a>
								</div>
								</div>

								<div class="cstr">
								<img src="<?php echo get_template_directory_uri() ?>/expimg/explore/bus-tracker.png" style="width: 180px; margin: 20px 0;" alt="Bus Tracker">
								<div class="tca-button-wrap blue">
									<a href="https://www.fairfaxcounty.gov/bustime/home.jsp" class="droid app tca-button" target="_blank">BusTracker</a>
								</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<section class="grad-bg">
				<div class="uk-container full-page section-content">
					<h2 class="script c-txt">Walk, Bike or Roll is for Everyone!</h2>
					<div class="tetraptych">
						<div class="item">
							<h3>Residents</h3>
							<dl>
								<dd>Don’t worry about parking when you walk to your favorite store or restaurant.</dd>
								<dd>Take metrorail to Tysons, then get around by bike or scooter.</dd>
							</dl>
						</div>
						<div class="item">
							<h3>Employees</h3>
							<dl>
								<dd>Stroll to happy hour after work!</dd>
								<dd>Walk, bike or roll to your midday meetings.</dd>
								<dd>Visit area businesses and restaurants for incentives when you Walk, Bike or Roll.</dd>
							</dl>
						</div>
						<div class="item">
							<h3>Employers</h3>
							<dl>
								<dd>Encourage your staff to walk to nearby meetings.</dd>
								<dd>Raise awareness about travel options and support services.</dd>
							</dl>
						</div>
						<div class="item">
							<h3>Visitors</h3>
							<dl>
								<dd>No need for bike racks or locks when you use Capital Bikeshare or Bird.</dd>
								<dd>A ticket or towing won’t spoil your visit when you walk, bike, or roll to your favorite store or restaurant.</dd>
							</dl>
						</div>
					</div>
				</div>
			</section>

			<section class="signup"  id="newsletter">
				<div class="uk-container full-page section-content">
					<h2 class="script">Sign up for the Explore Tysons<br> Weekly Email</h2>
					<h3 class="h3blu left">Sign up to receive weekly updates about the Walk, Bike, or Roll campaign. Be the first to know when new events and rewards are announced.</h3>
					<div class="row">
						
						<div class="col-md-6">
							<p>Help spread the word about the benefits of using various modes of transportation in our community.</p>
							<ul>
								<li>Feature your company on our partners page.</li>
								<li>Learn about the exciting Fall challenge coming later this year.</li>
								<li>Expand the ways people get around and experience Tysons.</li>
							</ul>
							<h4>Newsletter Archive</h4>
							<?php /*
							<style type="text/css">
								.display_archive a {
									color: #092c5b
								}
							</style>
							*/ ?>
							<script language="javascript" src="https://tysonsva.us21.list-manage.com/generate-js/?u=237c4325bc3c19cccbcea84f5&amp;show=10&amp;fid=536" type="text/javascript"></script>
						</div>

						<div class="col-md-6 form-contain">
							<?php gravity_form( 4, false, false, false, '', true, 2 );  ?>
							</div>
							
							<div id="partners"></div>
							<br>
						</div>
					</div>				
			</section>

			<section class="et-news-events">
				<div class="uk-container full-page section-content">
					<h2 class="script" style="margin:0;">Explore Tysons News &amp; Events</h2>
					<div class="d-flex flex-wrap justify-content-center">
						<div class="post-card">
							<div class="border">
								<div class="inner">
									<div class="image-wrap">
										<div class="spacer"></div>
										<a href="<?php echo site_url(); ?>/explore-tysons/explore-tysons-by-skateboarding">
											<img width="300" height="300" src="<?php echo site_url(); ?>/wp-content/uploads/2025/02/skateboards.jpg" class="attachment-gallery-square size-gallery-square wp-post-image" alt="" decoding="async" >
										</a>
									</div>
									<div class="card-info">
										<div class="top">
											<div class="cats">
												<span class="category-label">Explore Tysons</span> &nbsp;&nbsp;
											</div>
											<div class="date">
												<span class="post-date">Jul 30, 2024</span>
											</div>
										</div>
										<h3>
											<a href="<?php echo site_url(); ?>/explore-tysons/explore-tysons-by-skateboarding">Explore Tysons by Skateboarding</a>
										</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="post-card">
							<div class="border">
								<div class="inner">
									<div class="image-wrap">
										<div class="spacer"></div>
										<a href="<?php echo site_url(); ?>/explore-tysons/explore-tysons-by-rolling/">
											<img width="300" height="300" src="<?php echo site_url(); ?>/wp-content/uploads/2025/02/rolling.jpg" class="attachment-gallery-square size-gallery-square wp-post-image" alt="" decoding="async" >
										</a>
									</div>
									<div class="card-info">
										<div class="top">
											<div class="cats">
												<span class="category-label">Explore Tysons</span> &nbsp;&nbsp;
											</div>
											<div class="date">
												<span class="post-date">Jul 30, 2024</span>
											</div>
										</div>
										<h3>
											<a href="<?php echo site_url(); ?>/explore-tysons/explore-tysons-by-rolling/">Explore Tysons by Rolling</a>
										</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="post-card">
							<div class="border">
								<div class="inner">
									<div class="image-wrap">
										<div class="spacer"></div>
										<a href="<?php echo site_url(); ?>/explore-tysons/explore-tysons-by-bike/">
											<img width="300" height="300" src="<?php echo site_url(); ?>/wp-content/uploads/2025/02/explore-bike.jpg" class="attachment-gallery-square size-gallery-square wp-post-image" alt="" decoding="async" >
										</a>
									</div>
									<div class="card-info">
										<div class="top">
											<div class="cats">
												<span class="category-label">Explore Tysons</span> &nbsp;&nbsp;
											</div>
											<div class="date">
												<span class="post-date">Jul 30, 2024</span>
											</div>
										</div>
										<h3>
											<a href="<?php echo site_url(); ?>/explore-tysons/explore-tysons-by-bike/">Explore Tysons by Bike</a>
										</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="post-card">
							<div class="border">
								<div class="inner">
									<div class="image-wrap">
										<div class="spacer"></div>
										<a href="<?php echo site_url(); ?>/featured/july-20-2024-wheel-wise-building-confidence-on-two-wheels/">
											<img width="300" height="300" src="<?php echo site_url(); ?>/wp-content/uploads/2025/02/bus.jpg" class="attachment-gallery-square size-gallery-square wp-post-image" alt="" decoding="async" >
										</a>
									</div>
									<div class="card-info">
										<div class="top">
											<div class="cats">
												<span class="category-label">Explore Tysons</span> | <span class="category-label">Featured</span> &nbsp;&nbsp;
											</div>
											<div class="date">
												<span class="post-date">Jul 09, 2024</span>
											</div>
										</div>
										<h3>
											<a href="<?php echo site_url(); ?>/featured/july-20-2024-wheel-wise-building-confidence-on-two-wheels/">July 20, 2024: Wheel Wise – Building Confidence on Two Wheels</a>
										</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="post-card">
							<div class="border">
								<div class="inner">
									<div class="image-wrap">
										<div class="spacer"></div>
										<a href="<?php echo site_url(); ?>/explore-tysons/new-to-capital-bikeshare-attend-our-demos-and-try-your-first-ride-on-us">
											<img width="300" height="300" src="<?php echo site_url(); ?>/wp-content/uploads/2025/02/bikes.jpeg" class="attachment-gallery-square size-gallery-square wp-post-image" alt="" decoding="async" >
										</a>
									</div>
									<div class="card-info">
										<div class="top">
											<div class="cats">
												<span class="category-label">Explore Tysons</span> &nbsp;&nbsp;
											</div>
											<div class="date">
												<span class="post-date">Jul 08, 2024</span>
											</div>
										</div>
										<h3>
											<a href="<?php echo site_url(); ?>/explore-tysons/new-to-capital-bikeshare-attend-our-demos-and-try-your-first-ride-on-us">New to Capital Bikeshare? Attend Our Demos and Try Your First Ride on Us!</a>
										</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="post-card">
							<div class="border">
								<div class="inner">
									<div class="image-wrap">
										<div class="spacer"></div>
										<a href="<?php echo site_url(); ?>/explore-tysons/the-tysons-get-around-guide-is-here">
											<img width="300" height="300" src="<?php echo site_url(); ?>/wp-content/uploads/2025/02/getaroundmap.webp" class="attachment-gallery-square size-gallery-square wp-post-image" alt="" decoding="async" >
										</a>
									</div>
									<div class="card-info">
										<div class="top">
											<div class="cats">
												<span class="category-label">Explore Tysons</span> &nbsp;&nbsp;
											</div>
											<div class="date">
												<span class="post-date">Jul 08, 2024</span>
											</div>
										</div>
										<h3>
											<a href="<?php echo site_url(); ?>/explore-tysons/the-tysons-get-around-guide-is-here">The Tysons Get Around Guide is here!</a>
										</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="post-card">
							<div class="border">
								<div class="inner">
									<div class="image-wrap">
										<div class="spacer"></div>
										<a href="<?php echo site_url(); ?>/explore-tysons/supervisor-palchik-walks-the-walk">
											<img width="300" height="300" src="<?php echo site_url(); ?>/wp-content/uploads/2025/02/palchik.jpg" class="attachment-gallery-square size-gallery-square wp-post-image" alt="" decoding="async" >
										</a>
									</div>
									<div class="card-info">
										<div class="top">
											<div class="cats">
												<span class="category-label">Explore Tysons</span> &nbsp;&nbsp;
											</div>
											<div class="date">
												<span class="post-date">Jul 08, 2024</span>
											</div>
										</div>
										<h3>
											<a href="<?php echo site_url(); ?>/explore-tysons/supervisor-palchik-walks-the-walk">Supervisor Palchik Walks the Walk</a>
										</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="post-card">
							<div class="border">
								<div class="inner">
									<div class="image-wrap">
										<div class="spacer"></div>
										<a href="<?php echo site_url(); ?>/blog/tysons-blooms-during-pedal-with-petals-family-bike-ride">
											<img width="300" height="300" src="<?php echo site_url(); ?>/wp-content/uploads/2025/02/pedals.jpg" class="attachment-gallery-square size-gallery-square wp-post-image" alt="" decoding="async" >
										</a>
									</div>
									<div class="card-info">
										<div class="top">
											<div class="cats">
												<span class="category-label">Blog</span> | <span class="category-label">Explore Tysons</span> | <span class="category-label">Featured</span> | <span class="category-label">Initiative</span> &nbsp;&nbsp;
											</div>
											<div class="date">
												<span class="post-date">May 23, 2024</span>
											</div>
										</div>
										<h3>
											<a href="<?php echo site_url(); ?>/blog/tysons-blooms-during-pedal-with-petals-family-bike-ride">Tysons Blooms During Pedal with Petals Family Bike Ride</a>
										</h3>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<section class="yiftee">
				<div class="uk-container full-page section-content">
					<h2 class="script">
						Join the Tysons Community Alliance’s Yiftee Program Today!
					</h2>
					<p>
						<strong>The goal of our “Explore Tysons” initiative</strong> is to encourage sustainable modes of transportation like walking, biking, or rolling. We’re pairing this effort with incentives using Yiftee digital gift cards, a program crafted to invigorate our local economy in Tysons. With Yiftee eGift Cards, patrons can support their favorite Tysons establishments, including yours!
					</p>
					<p>
						<strong>Joining the Yiftee program is absolutely FREE for merchants.</strong>
					</p>
					<p>Together, let’s amplify community engagement, bolster our local economy, and forge enduring connections throughout Tysons. Contact <a href="mailto:transportation@tysonsva.org" style="color:blue">transportation@tysonsva.org</a> to register your business. </p>
				</div>
			</section>

			<section class="partners">
				<div class="uk-container full-page section-content">
					<h2 class="script" style="text-align:center;background:#fff;margin:50px 0 0 0;">Partners</h2>
					<div class="text-center">
						<p class="c-txt">We thank our partners who are joining us to promote Walk, Bike, or Roll in Tysons.</p>
						<div class="partner-logos">
							<div class="acf-gallery">
								<img width="300" height="180" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/FCCS_logo_color.webp" class="attachment-medium size-medium" alt="" decoding="async">
								<img width="300" height="126" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/2024-Inadev-Logo-FINAL_Full-Color_NO-Tag-copy-300x126.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="99" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Dalia-Logo-300x99.webp" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="82" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Capital-One-CENTER-logo_pms-300x82.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="224" height="300" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/FABB_logo_col_transparent_2MB-224x300.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="154" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Groundswell_Primary-Logo-300x154.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="300" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Shipgarten-Logo-1-300x300.webp" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="95" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Westwood-Village-Black-logo-300x95.webp" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="106" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Celebrate-Fairfax-Logo_Corporate-Exclamation-Point_Color-300x106.webp" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="166" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/CF_PARC-Logo_Color-300x166.webp" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="124" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Dranesville-District-300x124.webp" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="155" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Nouvellelogo-300x155.webp" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="147" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Hunter-Mill-Distric-Logo-Blue-01-300x147.webp" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="113" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Bunch-Bikes-300x113.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="143" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/waba-logo-300x143.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="269" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/boro-logo-300x269.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="300" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/logo_strictlyebikes-sm-300x300.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="236" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/archer-logo-300x236.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="120" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/Wyrd-Ryds-logo-300x120.jpg" class="attachment-medium size-medium" alt="" decoding="async" >
								<img width="300" height="73" src="<?php echo get_template_directory_uri() ?>/expimg/explore/partners/nordine-300x73.webp" class="attachment-medium size-medium" alt="" decoding="async" >
							</div>
							<!-- .acf-gallery -->
						</div>
						<br>
						<br>
					</div>
					
				</div>
			</section>

			<section class="outro">
				<div class="uk-container full-page section-content">
					<p>Would you like to promote multimodal transportation and its many benefits to the community through community newsletters and social media, flyers and postcards, or on-site events?</p>
					<p>Help make Tysons a healthier and more vibrant community while showcasing your commitment to sustainability and well-being. Let’s collaborate to make Tysons more connected and accessible for all. Partner logos will be featured above and included in other opportunities for outreach and engagement.</p>
					<p>If you are a Tysons property owner or manager, business owner or employer, or a membership organization, please email us at <a href="mailto:transportation@tysonsva.org" style="color:blue">transportation@tysonsva.org</a>. </p>
				</div>
			</section>

			<div class="uk-container deco-wrap standard-container">
				<div class="section-decoration">
					<svg id="section-graphic-1" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 554 646">
						<defs>
							<style>
								.shape-0 {
									fill: #260d66;
								}

								.shape-1 {
									fill: #00b852;
								}

								.shape-2 {
									fill: #9adcf7;
								}

								.shape-3 {
									fill: #385dff;
								}
							</style>
						</defs>
						<path class="shape-1" d="M0,181.9v121.3l121.2-121.3H0Z"></path>
						<path class="shape-2" d="M181.8,0l-90.9,90.9h250.7L432.5,0h-250.7Z"></path>
						<path class="shape-0" d="M0,433.6v212.2l250.7-250.8v-212.2L0,433.6Z"></path>
						<path class="shape-3" d="M341.6,91.9l-90.9,90.9h212.1v212.2l90.9-90.9V91.9h-212.1Z"></path>
					</svg>
				</div>
			</div>
		
	</main><!-- #main -->

<?php

get_footer();
