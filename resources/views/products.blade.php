<!DOCTYPE html>
<html>
    <head>
        <title>{{ config('app.name') }}</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta content='text/html;charset=utf-8' http-equiv='content-type'>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    </head>
    <body class="wrapper nav-collapsed">
        <div class="wrapper">
            <div class="main-panel">
                <div class="main-content">
                    <div class="content-wrapper">

						@foreach ($items->chunk(3) as $chunk)
						  <div class="row">
							@foreach ($chunk as $product)
							<div class="col-lg-4">
							  <div class="card">
							  <div class="card-body text-center p-4">
								<h5 class="card-title">{{$product->productname}}</h5>
								<p class="card-text">{{$product->sku}}</p>
								<p class="card-text">${{$product->price}}</p>
								<a href="#" class="btn btn-primary">Order Now</a>
							  </div>
							</div>
							</div>
							@endforeach
						  </div>
						@endforeach                            
									   
						<!-- end row -->

						<div class="row">
							<div class="col-lg-12">
								{!! $items->links('pagination::bootstrap-4') !!}
							</div>
						</div>
    

                    </div>
                </div>
            </div>
        </div>   
	</body>
</html>