<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">
                <i class="fa fa-fw fa-cogs"></i> Clutch
            </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                {{-- <li class="active"><a href="#">Home</a></li> --}}
            </ul>
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Filter" v-model="filter" autofocus>
                </div>
            </form>
            <p class="navbar-text">
                <span class="label label-warning">
                    <i class="fa fa-fw fa-pause"></i> @{{ counts.paused }}
                </span>
                <span class="label label-success" style="margin-left:5px;">
                    <i class="fa fa-fw fa-upload"></i> @{{ counts.seeding }}
                </span>
            </p>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>