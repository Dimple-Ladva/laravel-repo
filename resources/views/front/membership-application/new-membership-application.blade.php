
<main>
    <section class="hero__inner hero__inner-mb row mx-0 align-items-center  bg-gradient-transparent position-relative">
        <img class="bg-image" src="{{$public_images}}/front/PCA_MembersMedia0003.png" alt="">
        <div class="container">
            <div class="row justify-content-center">
                <h1 class="h1-xl text-capitalize align-items-center text-white fw-500">Join PCA</h1>
            </div>
        </div>
    </section>

    <!-- NEW SSECTION -->
    <section class="s-card-join">
        <div class="container">
            <div class="row">
                <p class="col-12 color-grey s-card-join__description">
                    As the premier business leadership organization for Downtown Phoenix – and the Membership affiliate of Downtown Phoenix Inc. – Phoenix Community Alliance immediately connects you with all that Downtown Phoenix has to offer. PCA members have greater access to information and opportunities that directly and positively affect their businesses, bottom lines, and impact in the community. Through a strong network of active Committees and vigorous calendar of Member-only and public events, PCA Members can engage in ways that align with their own strategies for growth and success.
                </p>
                <div class="col-lg-4 col-12">
                    <div class="s-card-join__card main-bg-card position-relative">
                        <h3 class="s-card-join__card-heading">
                            Access
                        </h3>
                        <ul class="s-card-join__card-list list-unstyled mb-0">
                            <li class="s-card-join__card-list-item">
                                Inform
                            </li>
                            <li class="s-card-join__card-list-item">
                                Engage
                            </li>
                            <li class="s-card-join__card-list-item">
                                Empower Key Stakeholders
                            </li>
                        </ul>
                        <a href="#" class="pseudo-link"></a>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="s-card-join__card second-bg-card position-relative">
                        <h3 class="s-card-join__card-heading">
                            Advocate
                        </h3>
                        <ul class="s-card-join__card-list list-unstyled mb-0">
                            <li class="s-card-join__card-list-item">
                                Develop
                            </li>
                            <li class="s-card-join__card-list-item">
                                Communicate
                            </li>
                            <li class="s-card-join__card-list-item">
                                Champion Key Policy Priorities
                            </li>
                        </ul>
                        <a href="#" class="pseudo-link"></a>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="s-card-join__card third-bg-card position-relative">
                        <h3 class="s-card-join__card-heading">
                            Build
                        </h3>
                        <ul class="s-card-join__card-list list-unstyled mb-0">
                            <li class="s-card-join__card-list-item">
                                Envision
                            </li>
                            <li class="s-card-join__card-list-item">
                                Garner Support
                            </li>
                            <li class="s-card-join__card-list-item">
                                Facilitate Key Projects
                            </li>
                        </ul>
                        <a href="#" class="pseudo-link"></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="s-accordion">
        <div class="container">
            <div class="row">
                <h2 class="col-12 secondary-color s-accordion__heading">Phoenix Community Alliance Benefits</h2>
                <div class="accordion-group col-12 color-grey accordion-style-small">
                    <div class="accordion-wrap accordion-wrap--first s-accordion__accordion-wrap--first">
                        <h4 class="accordion-title accordion-title-click s-accordion__accordion-title position-relative main-transition active tertiary-color">
                            Connect
                            <span class="accordion-title--pseudo"></span>
                        </h4>
                        <div class="accordion-content">
                            <ul>
                                <li>
                                    Member Mingles
                                </li>
                                <li>
                                    Workshops
                                </li>
                                <li>
                                    New Member Orientation / Membership Refresh
                                </li>
                                <li>
                                    Member to Member Program
                                </li>
                                <li>
                                    Quarterly Meetings
                                </li>
                                <li>
                                    Community Forums
                                </li>
                                <li>
                                    Member Hosted Events
                                </li>
                                <li>
                                    Collaboration with other Member Organizations
                                </li>
                                <li>
                                    Volunteer Program
                                </li>
                                <li>
                                    Member Portal on website
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div class="accordion-wrap">
                        <h4 class="accordion-title accordion-title-click s-accordion__accordion-title position-relative main-transition tertiary-color">
                            Inform
                            <span class="accordion-title--pseudo"></span>
                        </h4>
                        <div class="accordion-content">
                            <ul>
                                <li>
                                    Golf Cart Tour
                                </li>
                                <li>
                                    Member Minute
                                </li>
                                <li>
                                    Private Facebook Group
                                </li>
                                <li>
                                    Advocacy Alerts
                                </li>
                                <li>
                                    Presentations
                                </li>
                                <li>
                                    From the Desk of Devney Majerle
                                </li>
                                <li>
                                    Digital Weekly Downtown Insider
                                </li>
                                <li>
                                    Committee Invites and Reminders
                                </li>
                                <li>
                                    Voice of the Member
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="accordion-wrap">
                        <h4 class="accordion-title accordion-title-click s-accordion__accordion-title position-relative main-transition tertiary-color">
                            Promote
                            <span class="accordion-title--pseudo"></span>
                        </h4>
                        <div class="accordion-content">
                            <ul>
                                <li>
                                    PCA Website
                                </li>
                                <li>
                                    Social Media
                                </li>
                                <li>
                                    Sponsorships
                                </li>
                                <li>
                                    Hosting PCA Events
                                </li>
                                <li>
                                    Member Collaboration
                                </li>
                                <li>
                                    DPI Platform
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- ------------------- -->
    <section class="form-section container">
        <div class="row">
            <div class="col-12">
                <!--                <h4 class="tertiary-color text-uppercase mb-sm-5 mb-4">Phoenix Community Alliance membership is annual and Members will be auto-enrolled to renew.</h4>-->
                @if ($message = Session::get('error_msg'))
                    <div id="error-message" class="alert alert-dismissible fade show alert-danger" role="alert">
                        {{ $message }} <a href="{{route('sign-in')}}">Click here</a> to login.
                    </div>
                @endif

                @if ($message = Session::get('error_captcha_msg'))
                    <div id="error_captcha_msg" class="alert alert-dismissible fade show alert-danger" role="alert">
                        {{ $message }}
                    </div>
                    {{--<p class="text-danger font-weight-bold">{{ $message }}</p>--}}
                @endif
            </div>
            <div class="col-12">
                <form action="{{route('membership-application-proceed-payment','proceed-to-payment')}}" method="post" class="form cta-card" id="signup">
                    @csrf
                    <h3 class="secondary-color pt-4">Membership Details</h3>
                    <hr>
                    <div class="form-row">
                        <div class="form-group col-sm">
                            <label for="pcam_fname" class="d-block">First Name <span class="secondary-color fw-700">*</span></label>
                            <input type="text" placeholder="First Name" id="pcam_fname" name="pcam_fname" data-msg-required="Please enter the first name." class="form-control" value="{{old('pcam_fname')}}" required>
                        </div>
                        <div class="form-group col-sm">
                            <label for="pcam_lname" class="d-block">Last Name <span class="secondary-color fw-700">*</span></label>
                            <input type="text" placeholder="Last Name" id="pcam_lname" name="pcam_lname" data-msg-required="Please enter the last name." class="form-control" value="{{old('pcam_lname')}}" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm">
                            <label for="pcam_email" class="d-block">Email <span class="secondary-color fw-700">*</span></label>
                            <input type="email" placeholder="Email" id="pcam_email" name="pcam_email" data-msg-required="Please enter the email address."  class="form-control" value="{{old('pcam_email')}}" required>
                        </div>
                        <div class="form-group col-sm">
                            <label for="pcam_phone" class="d-block">Phone <span class="secondary-color fw-700">*</span></label>
                            <input type="text" placeholder="Phone" id="pcam_phone" name="pcam_phone" data-msg-required="Please enter the phone number." class="form-control  phone phone_no" value="{{old('pcam_phone')}}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pcam_title" class="d-block">Title</label>
                        <input type="text" placeholder="Title" id="pcam_title" name="pcam_title" class="mb-5 form-control" value="{{old('pcam_title')}}">
                    </div>
                    <h3 class="secondary-color">Membership Level</h3>
                    <hr>
                    <div class="form-group">
                        <label class="d-block mb-2">Membership Level <span class="secondary-color fw-700">*</span></label>
                        <div class="form-check pl-0 mb-2">

                            <input  class="form-check-input radio-custom radio-js rb-group-1-js" type="radio" name="pcam_level" id="mem_level1"  value="70"  @if(\Illuminate\Support\Facades\Request::old('pcam_level') == '' || \Illuminate\Support\Facades\Request::old('pcam_level') != '') checked @endif  data-value="{{old('pcam_level')}}">
                            <label class="form-check-label" for="mem_level1">
                                Company
                            </label>
                        </div>
                        <div class="form-check pl-0 mb-2">
                            <input   class="form-check-input radio-custom radio-js rb-group-1-js" type="radio" name="pcam_level" id="mem_level2"  value="73" @if( \Illuminate\Support\Facades\Request::old('pcam_level') != '' && \Illuminate\Support\Facades\Request::old('pcam_level') == 73) checked @endif data-value="{{old('pcam_level')}}">
                            <label class="form-check-label" for="mem_level2">
                                Retail
                            </label>
                        </div>
                        <div class="form-check pl-0 mb-2">
                            <input  class="form-check-input radio-custom radio-js rb-group-1-js" type="radio" name="pcam_level" id="mem_level3"  value="71" @if( \Illuminate\Support\Facades\Request::old('pcam_level') != '' && \Illuminate\Support\Facades\Request::old('pcam_level') == 71) checked @endif data-value="{{old('pcam_level')}}">
                            <label class="form-check-label" for="mem_level3">
                                Adjunct / Public / Non-Profit
                            </label>
                        </div>
                        <div class="form-check pl-0 mb-2">
                            <input  class="form-check-input radio-custom radio-js" type="radio" name="pcam_level" id="mem_level4"  value="72" @if( \Illuminate\Support\Facades\Request::old('pcam_level') != '' && \Illuminate\Support\Facades\Request::old('pcam_level') == 72) checked @endif data-value="{{old('pcam_level')}}">
                            <label class="form-check-label" for="mem_level4">
                                Individual
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="member_level" id="member_level" class="d-block">Company Membership Levels <span class="secondary-color fw-700">*</span></label>
                        <select  name="pcam_pm_id" class="form-control " data-msg-required="Please enter the company membership levels." id="pcam_pm_id"  required data-selected-subcategory="{{old('pcam_pm_id')}}">

                        </select>
                        <input type="hidden" name="pm_stripe_price_id" id="pm_stripe_price_id" value="">
                        <input type="hidden" name="pm_stripe_prod_id" id="pm_stripe_prod_id" value="">
                    </div>
                    <div class="form-group mb-5">
                        <label for="mem_total">Total:</label>
                        <input  class="form-control" type="text" name="pcam_total" id="pcam_total" readonly data-selected-total="{{old('pcam_total')}}">
                    </div>
                    <div class="select-list-js active" id="companyDetails">
                        <h3 class="secondary-color">Company Details</h3>
                        <hr>
                        <div class="form-group">
                            <label class="d-block">Company Name <span class="secondary-color fw-700">*</span></label>
                            <input type="text" placeholder="Company Name" name="pcam_company_name" id="pcam_company_name" data-msg-required="Please enter the company name." class=" form-control" value="{{old('pcam_company_name')}}" required>
                        </div>
                        <div class="form-group">
                            <label class="d-block">Company Description</label>
                            <textarea class="form-control form__textarea" name="pcam_company_desc" id="pcam_company_desc" placeholder="Business Description">{{old('pcam_company_desc')}}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="d-block">Company Address <span class="secondary-color fw-700">*</span></label>
                            <input type="text" placeholder="Company Street" name="pcam_company_street" id="pcam_company_street" data-msg-required="Please enter the company address." value="{{old('pcam_company_street')}}" class=" form-control" required>
                            <small class="form-text text-muted">Company Street</small>
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="Company Street Line 2" name="pcam_company_street2" value="{{old('pcam_company_street2')}}" id="pcam_company_street2" class=" form-control">
                            <small class="form-text text-muted">Company Street Line 2</small>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm">
                                <input type="text" placeholder="Company City" name="pcam_company_city" value="{{old('pcam_company_city')}}" id="pcam_company_city" class=" form-control">
                                <small class="form-text text-muted">Company City</small>
                            </div>
                            <div class="form-group col-sm">
                                <select class="form-control" name="pcam_company_state" id="pcam_company_state">
                                    @foreach($arrState as $key=>$val)
                                        @if($val->state_name == 'Arizona')
                                            <option value="{{$val->state_name}}"  selected="selected">{{$val->state_name}}  </option>
                                        @endif
                                        <option value="{{$val->state_name}}" >{{$val->state_name}}  </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Company State</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="Company Zip" name="pcam_company_zip" value="{{old('pcam_company_zip')}}" id="pcam_company_zip" class=" form-control">
                            <small class="form-text text-muted">Company Zip</small>
                        </div>
                        <div class="form-row mb-5">
                            <div class="form-group col-sm">
                                <label class="d-block">Company Phone <span class="secondary-color fw-700">*</span></label>
                                <input type="text" placeholder="Company Phone" name="pcam_company_phone" id="pcam_company_phone" data-msg-required="Please enter the company phone number." value="{{old('pcam_company_phone')}}" class=" form-control phone phone_no valid" required>
                            </div>
                            <div class="form-group  col-sm">
                                <label class="d-block">Company Website <span class="secondary-color fw-700">*</span></label>
                                <input type="text" placeholder="example: https://www.phoenixcommunityalliance.com" name="pcam_company_website" id="pcam_company_website" data-msg-required="Please enter the company website." class=" form-control" value="{{old('pcam_company_website')}}" required>
                                <small class="form-text text-muted">Please enter the whole URL including "http://"</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5 select-list-js" id="areasOfInterest">
                        <h3 class="secondary-color">Areas of Interest</h3>
                        <hr>
                        <div class="form-group">
                            <label class="d-block">Link / Website / Linkedin Url</label>
                            <input type="text" placeholder="Link / Website / Linkedin Url" name="pcam_quote_area_interest" id="pcam_quote_area_interest" class=" form-control" value="{{old('pcam_quote_area_interest')}}">
                        </div>
                    </div>

                    <h3 class="secondary-color">Individual Details</h3>
                    <hr>
                    <div class="form-group">
                        <label class="d-block">Committees of Interest <span class="secondary-color fw-700">*</span></label>
                    </div>
                    <div id="interestCheckboxGroup">
                        <label class="checkbox-custom">Arts, Culture & Public Life
                            <input type="checkbox" name="pcam_area_interest[]" id="pcam_area_interest" value="Arts, Culture & Public Life" @if(is_array(old('pcam_area_interest')) && in_array('Arts, Culture & Public Life', old('pcam_area_interest'))) checked @endif>
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox-custom">Central City Planning & Development
                            <input type="checkbox" name="pcam_area_interest[]" id="pcam_area_interest" value="Central City Planning & Development" @if(is_array(old('pcam_area_interest')) && in_array('Central City Planning & Development', old('pcam_area_interest'))) checked @endif>
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox-custom">Education
                            <input type="checkbox" name="pcam_area_interest[]" id="pcam_area_interest" value="Education" @if(is_array(old('pcam_area_interest')) && in_array('Education', old('pcam_area_interest'))) checked @endif>
                            <span class="checkmark"></span>
                        </label>
                        {{--<label class="checkbox-custom">McDowell Road Revitalization
                            <input type="checkbox" name="pcam_area_interest[]" id="pcam_area_interest" value="McDowell Road Revitalization" @if(is_array(old('pcam_area_interest')) && in_array('McDowell Road Revitalization', old('pcam_area_interest'))) checked @endif>
                            <span class="checkmark"></span>
                        </label>--}}
                        <label class="checkbox-custom">Membership Engagement
                            <input type="checkbox" name="pcam_area_interest[]" id="pcam_area_interest" value="Membership Engagement" @if(is_array(old('pcam_area_interest')) && in_array('Membership Engagement', old('pcam_area_interest'))) checked @endif>
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox-custom">Multi-Modal Connectivity
                            <input type="checkbox" name="pcam_area_interest[]" id="pcam_area_interest" value="Multi-Modal Connectivity" @if(is_array(old('pcam_area_interest')) && in_array('Multi-Modal Connectivity', old('pcam_area_interest'))) checked @endif>
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox-custom">Social & Housing Advancement
                            <input type="checkbox" name="pcam_area_interest[]" id="pcam_area_interest" value="Social & Housing Advancement" @if(is_array(old('pcam_area_interest')) && in_array('Social & Housing Advancement', old('pcam_area_interest'))) checked @endif>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="form-group pt-4 ">
                        <label class="d-block">Other Interests</label>
                        <input type="text" name="pcam_quote_other_interest" id="pcam_quote_other_interest" class=" form-control" value="{{old('pcam_quote_other_interest')}}">
                    </div>
                    {{--<div class="form-group mt-4 mb-4">
                         <div class="captcha">
                             <span>{!! captcha_img() !!}</span>
                             <button type="button" class="btn btn-danger bg-danger" class="reload" id="reload">
                                 ↻
                             </button>
                         </div>
                     </div>
                     <div class="form-group mb-4">
                         <input id="captcha" type="text" class="form-control" data-msg-required="Please enter the captcha." placeholder="Enter Captcha" name="captcha" required>
                     </div>
                     @error('captcha')
                     <p class="text-danger">{{ $message }}</p>
                     @enderror--}}
                    <div class="form-group mt-4 mb-4">
                        <div class="captcha">
                            <img src="{{route('captcha-image')}}">
                            <button onclick="reloadCaptcha('inquiry_capatch');" type="button" class="btn btn-danger bg-danger reload"  id="reload">
                                ↻
                            </button>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="code_of_image" value="" required>
                    </div>

                    <div class="form-group mt-4 mb-4 text-primary Term_chekbox" >
                        <label class="checkbox-custom">
                            Please confirm all information you entered is correct before clicking "Proceed to Payment" below. Once you proceed to payment, you will not be able to return to this page
                            <input id="Term_chekbox" class="form-check-input me-1" data-msg-required="Please read and accept Terms and Conditions before signup!" name="Term"  type="checkbox" value="Yes" required>
                            <p class="d-none" style="color:red" id="term_error">Please read and accept Terms and Conditions before signup!</p>
                            <span class="checkmark"></span>
                        </label>
                    </div>


                    {{--<div class="form-group pt-3">
                        <button type="submit" id="process-to-payment" class="btn form-btn" value="Proceed to Payment">Proceed to Payment</button>
                        <input type="hidden" id="" name="formtype" value="signup" class="btn form-btn">
                    </div>--}}
                    <div class="form-group pt-3 d-flex">
                        <button type="submit" id="process-to-payment" class="btn form-btn" value="Proceed to Payment">Proceed to Payment</button>
                        <div id="myDiv" style="text-align: center;">
                            <span id="loading-image" class="spinner-border text-primary spinner-border-sm mx-2" role="status" aria-hidden="true" style="width: 2rem; height: 2rem;display: none"></span>
                        </div>
                        <input type="hidden" id="" name="formtype" value="signup" class="">
                        <input type="hidden" id="member_session_id" name="member_session_id" value="@if(isset($member_session_id)){{$member_session_id}}@endif">
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

