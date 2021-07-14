@extends('layouts.app')
	@section('content')

            <section class="main-content-block">
                <article class="bread-crumbs"><a href="{{ url('/') }}">home</a> &map; offer manager</article>

                <section class="page-content-holder">
                    <nav class="accman-menu">
                        <ul>
                            <li class="user-info-sum">
                                <article class="photo">
                                    
                                    <h4>Kojo Amoafo-Yeboah</h4>
                                </article>
                                <article>
                                    <a href="{{ url('/trader/profile') }}"><i class="fa fa-edit"></i> edit profile</a>
                                </article>
                            </li>

                            <li>
                                <a href="{{ url('/trader/offers') }}"><i class="fa fa-list"></i> my offers</a>
                            </li>

                            <li>
                                <a href="{{ url('/trader/watchlist') }}"><i class="fa fa-eye"></i> my watchlist</a>
                            </li>
                        </ul>
                        
                    </nav>
                    <!--h1>About Batabred</h1-->

                    <section class="accman-content">
                        <h1>my offers</h1>
                        <?php 
                        $expTitle = explode(' ', $getOfferDetails->title);
                        $impTitle = implode('-', $expTitle);

                        $postURL = "/trader/offers/edit/{$getOfferDetails->offercode}/{$impTitle}"; ?>
                        <form method="POST" action="{{ url($postURL) }}" enctype="multipart/form-data" class="list-offerfrm">
                            {{ csrf_field() }}

                            <input type="hidden" name="unique_id" value="{!! $getOfferDetails->offercode !!}">

                            <h2 id="main-title">I'm offering</h2>
                            <article id="frm-caption">Fill the fields with details of your offer.</article>

                            <article>
                                <div>
                                    <label for="o_category" class="">Category<span id="ast">*</span></label>
                                </div>
                                <select name="o_category" required>
                                <?php
                                $cCnt = 1;
                                foreach ($getMainCategories as $mainCateg) {
                                    echo '<option value="' . $mainCateg->id . '"'; 
                                    echo ($getOfferDetails->categoryid == $mainCateg->id) ? 'selected="selected"' : '';
                                    echo '>' . $mainCateg->category_name . '</option>';
                                    $cCnt++;
                                }
                                ?>
                                </select>
                            </article>

                            <article>
                                <div>
                                    <label for="title" class="">Title<span id="ast">*</span></label>
                                </div>
                                <div>
                                    <input id="title" type="text" class="form-control" name="o_title" value="{{ $getOfferDetails->title }}" placeholder="" required>
                                </div>
                            </article>

                            <article>
                                <div>
                                    <label for="actValue" class="">Actual Value<span id="ast">*</span></label><br>
                                    <em>(Estimated cash value of your offer.)</em>
                                </div>
                                <div>
                                    GH&cent;
                                    <input id="actValue" type="text" class="form-control" name="o_actValue" style="width: 100px;" value="{{ $getOfferDetails->actual_value }}" placeholder="0" required>
                                </div>
                            </article>

                            <article>
                                <div>
                                    <label for="description" class="">Description<span id="ast">*</span></label>
                                </div>
                                <div>
                                    <textarea id="description" class="form-control" name="o_desc" placeholder="" required>{{ $getOfferDetails->description }}</textarea>
                                </div>
                            </article>

                            <article class="offer-photos">
                                
                                <div>
                                    <label for="description" class="">Upload Photos</label><br>
                                    <em id="frm-caption">(First photo will be your default display photo)</em>
                                </div>

                                <?php
                                $trimPhotos = trim($getOfferDetails->photos);
                                $expPhotos = explode(' ', $trimPhotos);

                                $photoCnt = 5;
                                $cnt = 0;

                                foreach ($expPhotos as $photo) {
                                    echo '
                                    <label class="picHolder">
                                        <article id="imgHolder' . $cnt . '" style="background-image: url(/photos/items/thumbs/'. $photo .'); cursor: default;">
                                        </article>
                                        <!--input type="file" name="o_photos[]" onchange="readURL(this, \'#imgHolder' . $cnt . '\');" /-->

                                        <input type="hidden" name="checkPhotoName[]" value="'. $photo .'">

                                    </label>';
                                    $photoCnt--;
                                }

                                for($i = 0; $i < $photoCnt; $i++) {
                                    echo '
                                    <label class="picHolder">
                                        <article id="imgHolder' . $i . '">
                                            <i class="fa fa-camera"></i>
                                        </article>
                                        <input type="file" name="o_photos[]" onchange="readURL(this, \'#imgHolder' . $i . '\');" />
                                        
                                    </label>';
                                }?>
                            </article>



                            <script type="text/javascript">
                            function readURL(input, imgHolder) {
                                $(imgHolder).empty();

                                if (input.files && input.files[0]) {

                                    var imgPath = $(input)[0].value;
                                    var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

                                    
                                    if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                                        if (typeof (FileReader) != "undefined") {
                                            var reader = new FileReader();

                                            reader.onload = function (e) {
                                                
                                                $("<img />", {
                                                    "src": e.target.result,
                                                    "class": "thumb-image"
                                                }).appendTo(imgHolder);

                                                /*$("<input />", {
                                                    "type": "radio",
                                                    "name": "def_img",
                                                    "class": "default-img",
                                                    "checked": "checked",
                                                    "value": "Default"
                                                }).appendTo(imgHolder);*/

                                            }
                                            $(imgHolder).show();

                                            $(imgHolder+" .fa-camera").hide();
                                            reader.readAsDataURL(input.files[0]);

                                        } else {
                                            alert("This browser does not support FileReader.");
                                        }

                                    } else {
                                        alert("Pls select only images");
                                    }
                                }
                            }
                            </script>


                            <h2 id="main-title">I need</h2>
                            <article id="frm-caption">Fill the fields with details of what you want to barter your offer for.</article>

                            <?php
                            $wCnt = 0;
                            $getWantedList = DB::table("bb_wanted")
                                ->select("*")
                                ->where("offercode", "=", $getOfferDetails->offercode)
                                ->get();

                            foreach($getWantedList as $wanted) {
                                $wCnt++;

                                if($wCnt == 1) {
                                    $ast = '<span id="ast">*</span>';
                                    $required = "required";
                                    echo '<article id="tag">Option ' . $wCnt . '' . $ast . '</article>';
                                } else {
                                    $ast = "";
                                    $required = "";
                                    echo '<article id="tag" style="display: block; margin-top: 80px;">Option ' . $wCnt . '' . $ast . '</article>';
                                }
                            ?>

                            <article>
                                <div>
                                    <label for="w_category_{!! $wCnt !!}" class="">Category{!! $ast !!}</label>
                                </div>
                                <select name="w_category_{!! $wCnt !!}" {!! $required !!}>
                                <?php
                                $cCnt = 1;
                                foreach ($getMainCategories as $wMainCateg) {
                                    echo '<option value="' . $wMainCateg->id . '"'; 
                                    echo ($wanted->categoryid == $wMainCateg->id) ? 'selected="selected"' : '';
                                    echo '>' . $wMainCateg->category_name . '</option>';
                                    $cCnt++;
                                }
                                ?>
                                </select>
                            </article>

                            <article>
                                <div>
                                    <label for="w_title_{!! $wCnt !!}" class="">Title{!! $ast !!}</label>
                                </div>
                                <div>
                                    <input id="w_title_{!! $wCnt !!}" type="text" class="form-control" name="w_title_{!! $wCnt !!}" value="{{ $wanted->title }}" placeholder="" {!! $required !!}>
                                </div>
                            </article>

                            <article>
                                <div>
                                    <label for="w_addCash_{!! $wCnt !!}" class="">Additional Cash</label><br>
                                    <em>Additional amount to make up for the actual value.</em>
                                </div>
                                <div>
                                    GH&cent;
                                    <input id="w_addCash_{!! $wCnt !!}" type="text" class="form-control" name="w_addCash_{!! $wCnt !!}" style="width: 100px;" value="{{ $wanted->addcash }}" placeholder="0">
                                </div>
                            </article>

                            <input type="hidden" name="w_wantCode_{!! $wCnt !!}" value="{!! $wanted->wantcode !!}">
                            <?php
                            }
                            ?>

                            <article>
                                <div>
                                    <label for="title" class="">Type of Barter</label>
                                </div>
                                
                                
                                <div style="margin: 5px 0;">
                                    <input id="o_barterType" type="radio" class="form-control" name="o_barterType" value="1" {{ ($getOfferDetails->barter_type == 1) ? 'checked="checked"' : '' }}> Limited Barter <em>(I want what is on my "I Need" list only)</em>
                                </div>
                                
                                <div style="margin: 5px 0;">
                                    <input id="o_barterType" type="radio" class="form-control" name="o_barterType" value="2" {{ ($getOfferDetails->barter_type == 2) ? 'checked="checked"' : '' }}> Open Barter <em>(I want what is on my "I Need" list and any offer within the actual value)</em>
                                </div>
                                
                            </article>



                            <h2 id="main-title">Contact Details</h2>
                            <article id="frm-caption">You can change your contact information in your profile.</article>

                            <article>
                                <div>
                                    <label for="o_contactBy" class="">Contact Me By</label>
                                </div>
                                <div>
                                
                                    <input id="o_contactBy" type="radio" class="form-control" name="o_contactBy" value="1" {{ ($getOfferDetails->contactby == 1) ? 'checked="checked"' : '' }}> Both
                                    
                                    <span id="sep"></span>
                                    <input id="o_contactBy" type="radio" class="form-control" name="o_contactBy" value="2" {{ ($getOfferDetails->contactby == 2) ? 'checked="checked"' : '' }}> Inbox
                                    <span id="sep"></span>
                                    
                                    <input id="o_contactBy" type="radio" class="form-control" name="o_contactBy" value="3" {{ ($getOfferDetails->contactby == 3) ? 'checked="checked"' : '' }}> Phone
                                    
                                </div>
                            </article>

                            <article class="">
                                <button type="submit">
                                    Save Changes
                                </button>
                            </article>
                        </form>
                        
                    </section>
                </section>
            </section>
	@endsection