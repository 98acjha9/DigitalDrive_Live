<style>
#keywords{
  padding: 10px 20px;
    width: 100%;
    border: none;
    box-shadow: none;
    /* background-color: transparent; */
    background-image: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    font-family: 'Muli', sans-serif;
    font-size: 16px;
    font-weight: 600;
    color: #8c8c8c;
}
</style>
<main id="main"> 
  <!--==========================
      ConterDiv Section
    ============================-->
  <section id="postDiv">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
          <div class="task_Left">
            <div class="uiDiv">
              <h2>Earnings</h2>
              <div class="row">
                <div class="col-div-5 mb-0">
                  <div class="task_Left_Div blue">
                    <label>Net Income</label>
                    <a href="<?php base_url(); ?>freelancerearnings/netincome">
                    <h4>$<?php echo isset($net_income_count[0]["netincome"])? number_format($net_income_count[0]["netincome"],2) : 00; ?></h4>
                    </a>
                  </div>
                </div>
                <div class="col-div-5 mb-0">
                  <div class="task_Left_Div blue">
                    <label>In Escrow</label>
                    <a href="<?php base_url(); ?>freelancerearnings/inescrow">
                    <h4>$<?php echo isset($inescrow_count[0]["inescrow"])? number_format($inescrow_count[0]["inescrow"],2) : 00; ?></h4>
                    </a>
                  </div>
                </div>
                <div class="col-div-5 mb-0">
                  <div class="task_Left_Div blue">
                    <label>Budget</label>
                    <a href="<?php base_url(); ?>freelancerearnings/budget">
                    <h4>$<?php echo isset($budget_count[0]["budget"])? number_format($budget_count[0]["budget"],2) : 00; ?></h4>
                    </a>
                  </div>
                </div>
                <!-- <div class="col-div-5 mb-0">
                  <div class="task_Left_Div blue">
                    <label>Pending Clearance</label>
                    <h4>$250.00</h4>
                  </div>
                </div>
                <div class="col-div-5 mb-0">
                  <div class="task_Left_Div blue">
                    <label>Available For Withdrawl</label>
                    <h4>5</h4>
                  </div>
                </div> -->
              </div>
              <div class="row mb-5">
                
                <div class="col-md-7 pflBox3">
                  <div class="row">
                    <div class="col-md-12"><h5>Withdraw</h5></div>
                    <div class="col-md-5"><a href="close-contract.html" class="btn py-3 px-4 btn-block btn-outline-primary" disabled><img src="img/paypal.png" width="20" class="mr-2"/>Paypal Account</a></div>
                    <div class="col-md-5"><a href="#myModal" class="btn py-3 px-4 btn-block btn-outline-primary">Bank Transfer</a></div>
                  </div>
                </div>
              </div>
              <div class="row">
              
                <!-- Search form -->

                <div class="col-md-2">
                  <div class="keywords">
                    <input type="text" id="keywords" placeholder="Search keywords to filter" onkeyup="searchFilter();"/>  
                  </div>             	
                </div>
                <div class="col-md-2">
              		<div class="select-style">
                      <select id="sortBy" onchange="searchFilter();">
                        <option value="">Sort by Title</option>
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                      </select>
                  </div>
              	</div>
              	
              	<!-- <div class="col-md-2">
              		<div class="select-style">
                    <select>
                      <option>2020</option>
                      <option>2021</option>
                    </select>
                  </div>
              	</div>
              	<div class="col-md-3">
              		<div class="select-style">
                    <select>
                      <option>All Months</option>
                      <option>Save proposal</option>
                    
                    </select>
                  </div>
              	</div> -->
              	<div class="col-md-3 text-right">
              		<a href="#">Export CSV</a>
              	</div>
              </div>
            </div>
          </div>
            
          		<!-- Loading Image -->
              <div class="loading" style="display: none;">
                <div class="content" style="position: absolute;left: 40%;top: 60%;z-index: 29;"><img src="<?php echo base_url('assets/images/loading.gif'); ?>"/></div>
              </div>

          <div class="mbl-table-nine">
            <div class="mbldiv-scroll">
              <table class="table">
                <thead>
                  <tr>
                    
                    <th>Date</th>
                    <th>Project Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="dataList">
                <!-- Display posts list -->
			            <?php if(!empty($posts)){ foreach($posts as $row){ ?>
                  <tr>
                  	<td> <?php  echo date('Y-m-d',strtotime($row["created_date"])); ?></td>
                    <td><?php  echo $row["task_name"]; ?></td>
                    
                    <td><?php echo $row["amount"]; ?></td>
                    <td><a data-toggle="tooltip" data-placement="top" title="withdraw"><i class="fa fa-money" style="font-size:24px;"></i></a> 
                        <a data-toggle="tooltip" data-placement="top" title="Details" href="<?php echo base_url() ;?>hired-job-details/<?php echo $row['user_task_id'] ;?>">
                          <i class="fa fa-eye" style="font-size:24px;" aria-hidden="true"></i>
                          </a>
                  </td>
                    
                  </tr>
                  <!-- <tr>
                    <td><img src="img/cal-img.png" alt=""> 10/12/2019 </td>
                    <td>UI Design</td>
                    
                    <td>$50</td>
                  </tr> -->
                    <?php } }else{ ?>
                    <tr><td  colspan="4"><p>Records not found...</p></td></tr>
                  <?php } ?>
                      <!-- Render pagination links -->
                    <tr>
                    <td  colspan="4">
                      <?php echo $this->ajax_pagination->create_links(); ?>
                      
                    </td>
                    </tr>
                  
                </tbody>
              </table>
              
            </div>
            
          </div>
          
        </div>
        
      </div>
    </div>
    </div>
  </section>
</main>
<div class="myModal-newdiv">
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content"> 
        
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Raise Problem</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <div class="bod-sec">
            <h2> Frelancer </h2>
            <div class="img2-ses"> <span> <img src="img/view-all-offers-img.jpg" alt="img">
              <div class="round"></div>
              </span>
              <div class="caption">
                <h3> Bob Frapples </h3>
                <p> Sydney, NSW, Australia </p>
                <small>+ 2 Coins </small> 
                <small>- 2 Coins </small> 
                
                </div>
            </div>
            <div class="radiodiv">
              <ul>
                <li>
                  <label class="containerdiv"> I am not happy with the freelancer
                    <input type="radio" checked="checked" name="radio">
                    <span class="checkmark"></span> </label>
                </li>
                <li>
                  <label class="containerdiv"> Other reason
                    <input type="radio" checked="checked" name="radio">
                    <span class="checkmark"></span> </label>
                </li>
              </ul>
              <textarea rows="" cols="" placeholder="Describe your  problem here"></textarea>
              <div class="attach">
                <ul id="media-list" class="clearfix">
                  <li> <img src="img/add-plus-img2.jpg" alt="img"> </li>
                  <li class="myupload"> <span><i class="fa fa-plus" aria-hidden="true"></i>
                    <input type="file" click-type="type2" id="picupload" class="picupload" multiple="">
                    </span> </li>
                </ul>
              </div>
            </div>
            <div class="note"> <span> <img src="img/img-sec2.png" alt="img"> </span>
              <p> Note: Nlauncer support team will fix the problem through
                call or chat with both buddy with in 4 to 5 days </p>
            </div>
          </div>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal"> Send it to Hire-n-Work </button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="myModal-newdiv2">
  <div class="modal" id="myModal6">
    <div class="modal-dialog">
      <div class="modal-content"> 
        
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Add milestone</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <div class="add-mileston">
            <li class="row">
              <div class="col-lg-4 col-md-12 col-xs-12">
                <label>Title</label>
                <input type="text" class="form-control" placeholder="Wire Frame Design">
              </div>
              <div class="col-lg-4 col-md-12 col-xs-12">
                <label>Due Date</label>
                <div id="datepicker" class="input-group date" data-date-format="mm-dd-yyyy">
                  <input class="form-control" type="text" value="2012-12-12" readonly="">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span> </div>
              </div>
              <div class="col-lg-4 col-md-12 col-xs-12">
                <label>Amount</label>
                <div class="input-group amt"> <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                  <input class="form-control" type="text" placeholder="250" style="border:none; padding-left:0;">
                </div>
              </div>
              <div> </div>
            </li>
            <div class="radiodiv">
              <ul>
                <li>
                  <label class="containerdiv"> Deposit the fund later
                    <input type="radio" checked="checked" name="radio">
                    <span class="checkmark"></span> </label>
                </li>
                <li>
                  <label class="containerdiv"> Deposit the fund Now
                    <input type="radio" checked="checked" name="radio">
                    <span class="checkmark"></span> </label>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal"> Add </button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="myModal-newdiv3">
  <div class="modal" id="myModal7">
    <div class="modal-dialog">
      <div class="modal-content"> 
        
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Change Date</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <div class="add-mileston">
            <div class="row">
              <div class="col-lg-12 col-md-12 col-xs-12">
                <label>Select Date</label>
                <div id="ChangeDatePicker" class="input-group date" data-date-format="dd-mm-yyyy">
                  <input class="form-control" type="text" value="" readonly="">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span> </div>
              </div>
              <div> </div>
            </div>
          </div>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal"> Save </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery library -->
<!-- <script src="<?php //echo base_url('assets/js/jquery.min.js'); ?>"></script> -->
	
	<script>
	function searchFilter(page_num){
		page_num = page_num?page_num:0;
		var keywords = $('#keywords').val();
		var sortBy = $('#sortBy').val();
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url('freelancerearnings/ajaxPaginationData/'); ?>'+page_num,
			data:'page='+page_num+'&keywords='+keywords+'&sortBy='+sortBy,
			beforeSend: function(){
				$('.loading').show();
			},
			success: function(html){
        // console.log(html)
				$('#dataList').html(html);
				$('.loading').fadeOut("slow");
			}
		});
	}
	</script>
<!-- 
<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();   
});
</script> -->