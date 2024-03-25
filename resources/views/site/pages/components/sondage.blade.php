<style>
    .messages-wrap {
        margin: 10px auto;
        width: 100%;
        padding: 10px 0;
        text-align: center;
        /* background: #ffffff; */
        /* border: 2px solid #ddd; */
    }

    .message {
        display: none;
    }

    .message:first-child {
        display: block;
    }

    .animated {
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
    }

    @-webkit-keyframes fadeInDown {
        from {
            opacity: 0;
            -webkit-transform: translate3d(0, -100%, 0);
            transform: translate3d(0, -100%, 0);
        }

        100% {
            opacity: 1;
            -webkit-transform: none;
            transform: none;
        }
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            -webkit-transform: translate3d(0, -100%, 0);
            transform: translate3d(0, -100%, 0);
        }

        100% {
            opacity: 1;
            -webkit-transform: none;
            transform: none;
        }
    }

    .fadeInDown {
        -webkit-animation-name: fadeInDown;
        animation-name: fadeInDown;
    }
</style>



<div class="aside-block bg-white p-2">
    <div class="d-flex justify-content-between">
         <h3 class="aside-title">Sondages
        </h3>
        <a href="/post?category=sondage" class="text-capitalize" href="">Tous voir <i class="bi bi-arrow-bar-right text-bold"></i> </a>
    </div>
    <div class="messages-wrap">
        @foreach ($sondage_front as $item)
            <h6 class="message animated fadeInDown">{!! substr(strip_tags($item->description), 0, 150) !!}.... 
            <a href="/post/detail?slug={{ $item['slug'] }}" class="btn btn-dark mt-3">Participer</a>
            </h6>
        @endforeach
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {

        //Elements to loop through
        var elem = $('.message');
        //Start at 0
        i = 0;

        function getMessage() {

            //Loop through elements
            $(elem).each(function(index) {

                if (i == index) {
                    //Show active element
                    $(this).show();
                } else if (i == $(elem).length) {
                    //Show message
                    $(this).show();
                    //Reset i lst number is reached
                    i = 0;
                } else {
                    //Hide all non active elements
                    $(this).hide();
                }

            });

            i++;

        }

        //Run once the first time
        getMessage();

        //Repeat
        window.setInterval(getMessage, 7000);

    });
</script>
