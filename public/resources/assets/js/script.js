$(document).ready(function() {
        $(window).load(function() {
            $('.c-gallery__items img').click(function() {
                var src = $(this).attr('src');
                $('.c-gallery__img img').attr('src', src);
            });
            $("#modalBody").scrollTop($("#modalBody").prop("scrollHeight"));
        });
    } // document-ready
)
document.querySelector('.fa-bars').parentElement.addEventListener('click', () => {
    // backdrop.classList.add('show');
});

var baseUrl = "https://star4.ir";
var myVar;
function setAdminStuffForAdmin(element) {
    $(element).find('input:radio').prop('checked', true);
    let input = $(element).find('input:radio');
    let adminType = input.val().split("_")[1];
    let adminId = input.val().split("_")[0];
    $("#adminSn").val(adminId);
    $.ajax({
        method: 'get',
        url: baseUrl + "/getAdminTodayInfo",
        data: {
            _token: "{{ csrf_token() }}",
            asn: adminId
        },
        async: true,
        success: function(arrayed_result) {
            $("#adminCustomers").empty();
            moment.locale('en');
            let info = arrayed_result[0];
            let customers = arrayed_result[1];
            let peopels = arrayed_result[2];
            $("#loginTimeToday").text("");
            $("#adminName").text("");
            $("#countCommentsToday").text(0);
            $("#countFactorsToday").text(0);
            $("#countCustomersToday").text(0);
            $("#loginTimeToday").text(moment(peopels[0].loginTime , 'YYYY/M/D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D'));
            $("#adminName").text(info.name + ' ' + info.lastName);
            $("#countCommentsToday").text(peopels[0].countComments);
            $("#countFactorsToday").text(peopels[0].countFctors);
            $("#countCustomersToday").text(peopels[0].countCustomers);
            $("#adminCustomers").empty();
            customers.forEach((element, index) => {
                let maxHour = 0;
                let countFactor = 0;
                if (element.maxHour != null) {
                    maxHour =  moment(element.maxHour ,'YYYY/M/D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D');
                }
                if (element.countFactor != null) {
                    countFactor = element.countFactor;
                }
                $("#adminCustomers").append(`
            <tr>
            <td>` + (index + 1) + `</td>
            <td>` + element.Name + `</td>
            <td>` + maxHour + `</td>
            <td>` + countFactor + `</td>
            </tr>`);
            });
        },
        error: function(data) {}
    });
}
$("#returnComment").on("click",()=>{
    $.ajax({
        method: 'get',
        url: baseUrl + "/viewReturnComment",
        data: {
            _token: "{{ csrf_token()}}",
            csn: $("#customerSn").val()
        },
        async: true,
        success: function(arrayed_result) {
            $("#returnView").text(arrayed_result);
            $("#returnViewComment").modal("show");
        },
        error: function(data) {}
    });
});
$("#openkarabarDashboard").on("click", () => {
    $("#waitToDashboard").css("display",'flex');
    let asn = $("#adminSn").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/adminDashboard",
        data: {
            _token: "{{ csrf_token() }}",
            asn: asn
        },
        async: true,
        success: function(arrayed_result) {
            moment.locale('en');
            let admin = arrayed_result[0];
            let info = arrayed_result[1];
            let history = arrayed_result[2];
            let customers = arrayed_result[3];
            let sumAllReturnedFactor=0;
            if(info[0].totalReturnMoneyHds){
                sumAllReturnedFactor=parseInt(parseInt(info[0].totalReturnMoneyHds)/10);
            }
            if(admin[0].minDate){
                $("#assignCustomerDate").val(moment(admin[0].minDate, 'YYYY/M/D').locale('fa').format('YYYY/M/D'));
            }else{
                $("#assignCustomerDate").val("");  
            }
            $("#countCustomer").val(admin[0].countPeopel);
            $("#countCustomerBought").val(info[0].boughtPeopelsCount);
            $("#countFactors").val(parseInt(info[0].countFactor)+parseInt(info[0].countReturnFactor));
            $("#allMoneyFactor").val(parseInt(parseInt(info[0].totalMoneyHds/ 10)+parseInt(sumAllReturnedFactor)).toLocaleString("en-us")+" تومن");
            $("#lastMonthAllFactorMoney").val(parseInt(info[0].lastMonthFactorAllMoney/ 10).toLocaleString("en-us")+" تومن");
            if(info[0].lastMonthReturnedAllMoney){
            $("#lastMonthAllFactorMoneyReturned").val(parseInt(info[0].lastMonthReturnedAllMoney/ 10).toLocaleString("en-us")+" تومن");
            }else{
            $("#lastMonthAllFactorMoneyReturned").val("0 تومن");
             }
            $("#countReturnedFactor").val(info[0].countReturnFactor);
            $("#allMoneyReturnedFactor").val((sumAllReturnedFactor).toLocaleString("en-us")+" تومن");
            $("#notlogedIn").val(0);
            $("#comment").val(admin[0].discription);
            $("#adminNameModal").text(admin[0].name + ' ' + admin[0].lastName);
            $('#factorTable').empty();
            history.forEach((element, index) => {
                $('#factorTable').append(`
                <tr>
                <td>` + (index + 1) + `</td>
                <td>` + element.countPeople + `</td>
                <td>` + element.countBuyPeople + `</td>
                <td>` + element.countFactor + `</td>
                <td>`+ parseInt(element.lastMonthReturnedAllMoney / 10).toLocaleString("en-us")+` تومن`  +`</td>
                <td>` + parseInt(element.factorAllMoney / 10).toLocaleString("en-us")+` تومن`  + `</td>
                 <td>` + parseInt(element.lastMonthAllMoney / 10).toLocaleString("en-us")+` تومن` + `</td>
                <td>` + (element.meanIncrease*100).toLocaleString("en-us")+` </td>
                <td>`+element.noCommentCust+`</td>
                <td>`+element.noDoneWork+`</td>
                <td  onclick="showAdminComment(`+admin[0].id+`,'`+element.timeStamp+`')"><input name="factorId" style="display:none" type="radio" value="` + admin[0].id + `" /><i class="fa fa-eye" /> </td>
                </tr>
                `);
            });
            $("#lastMonthActions").empty();
            customers.forEach((element, index) => {
                $("#lastMonthActions").append(`
                <tr>
                <td>` + element.countCustomers + `</td>
                <td>` + element.countAllFactor + `</td>
                <td>` + parseInt(parseInt((element.sumAllFactor / 10)+(element.sumAllReturnedFactor / 10))).toLocaleString("en-us")+` تومن` + `</td>
                <td>`+parseInt(element.sumAllReturnedFactor / 10).toLocaleString("en-us")+` تومن` +`</td>
                <td>` +parseInt(parseInt(element.sumAllFactor / 10)).toLocaleString("en-us")+` تومن` + `</td>
              </tr>
              `);
            });
            $("#waitToDashboard").css("display",'none');
            $("#karbarAction").modal("show");
        },
        error: function(data) {}
    });
});
function showAdminComment(id,timeStamp) {
    $.ajax({
        method: 'get',
        url: baseUrl + "/getAdminHistoryComment",
        data: {
            _token: "{{ csrf_token() }}",
            timeStamp: timeStamp,
            id:id
        },
        async: true,
        success: function(arrayed_result) {
            // alert(arrayed_result.comment);
            $("#discription").text(arrayed_result.comment);
            $("#readDiscription").modal("show");
        },
        error: function(data) {}
    });
}
function showFactorDetails(element) {
    $(element).find('input:radio').prop('checked',true);
    let input = $(element).find('input:radio');
    $('tr').removeClass('selected');
    $(element).parent("tr").toggleClass('selected');
    $.ajax({
        method: 'get',
        url: baseUrl + "/getFactorDetail",
        data: {
            _token: "{{ csrf_token() }}",
            FactorSn: input.val()
        },
        async: true,
        success: function(arrayed_result) {
            let factor = arrayed_result[0];
            if(arrayed_result[0]){
            $("#factorDate").text(factor.FactDate);
            }
            $("#customerNameFactor").text(factor.Name);
            $("#customerComenter").text(factor.Name);
            $("#customerAddressFactor").text(factor.peopeladdress);
            $("#customerPhoneFactor").text(factor.hamrah);
            $("#factorSnFactor").text(factor.FactNo);
            $("#Admin").text(factor.name+' '+factor.lastName);
            $("#productList").empty();
            arrayed_result.forEach((element, index) => {
                $("#productList").append(`<tr>
                <td>` + (index + 1) + `</td>
                <td>` + element.GoodName + ` </td>
                <td>` + element.Amount / 1 + `</td>
                <td>` + element.UName + `</td>
                <td>` + (element.Fi / 10).toLocaleString("en-us") + `</td>
                <td>` + ((element.Fi /10)*(element.Amount/1)).toLocaleString("en-us") + `</td>
                </tr>`);
            });

            $("#factorDate1").text(factor.FactDate);
            $("#customerNameFactor1").text(factor.Name);
            $("#customerComenter1").text(factor.Name);
            $("#customerAddressFactor1").text(factor.peopeladdress);
            $("#customerPhoneFactor1").text(factor.hamrah);
            $("#factorSnFactor1").text(factor.FactNo);
            $("#Admin1").text(factor.name+' '+factor.lastName);
            $("#productList1").empty();
            arrayed_result.forEach((element, index) => {
                $("#productList1").append(`<tr>
                <td>` + (index + 1) + `</td>
                <td>` + element.GoodName + ` </td>
                <td>` + element.Amount / 1 + `</td>
                <td>` + element.UName + `</td>
                <td>` + (element.Fi / 10).toLocaleString("en-us") + `</td>
                <td>` + ((element.Fi /10)*(element.Amount/1)).toLocaleString("en-us") + `</td>
                </tr>`);
            });
            $("#viewFactorDetail").modal("show");
        },
        error: function(data) {}
    });
}

function setBargiryStuff(element) {
    $(element).find('input:radio').prop('checked', true);
    let input = $(element).find('input:radio');
    let factorId = input.val();
    $("#factorId").val(factorId);
    $.ajax({
        method: 'get',
        url: baseUrl + "/getFactorInfo",
        data: {
            _token: "{{ csrf_token() }}",
            fsn: factorId
        },
        async: true,
        success: function(arrayed_result) {
            $('#productList').empty();
            arrayed_result.forEach((element, index) => {
                $('#productList').append(`
                <tr>
                <td scope="col">` + (index + 1) + `</td>
                <td scope="col">` + element.GoodName + `</td>
                <td scope="col">` + element.Amount + `</td>
                <td scope="col">` + element.UName + `</td>
                <td scope="col">` + (element.Fi / 10).toLocaleString("en-us") + `</td>
                <td scope="col">` + (element.Price / 10).toLocaleString("en-us") + `</td>
              </tr>
        `);
            });
        },
        error: function(data) {}
    });
}
function setAdminStuff(element) {
    $(element).find('input:radio').prop('checked', true);
    let input = $(element).find('input:radio');
    let adminType = input.val().split('_')[1];
    let id = input.val().split('_')[0];
    $("#asn").val(id);
    $("#AdminForAdd").val(id);
    $("#adminTakerId").val(id);
    if ((adminType >1 & adminType <4)) {
        $("#customerContainer").css("display", "flex");
        $.ajax({
            method: 'get',
            url: baseUrl + "/getCustomer",
            data: {
                _token: "{{ csrf_token() }}"
            },
            async: true,
            success: function(arrayed_result) {
                
                $('#allCustomer').empty();
                
                arrayed_result.forEach((element, index) => {
                    $('#allCustomer').append(`
                <tr onclick="checkCheckBox(this,event)">
                    <td style="">` + (index + 1) +  `</td>
                    <td style="">` + element.PCode +  `</td>
                    <td>` + element.Name + `</td>
                    <td style="">
                    <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` + element.PSN + `" id="customerId">
                    </td>
                </tr>
            `);
                });

            },
            error: function(data) {}
        });
        $.ajax({
            method:'get',
            url: baseUrl + "/getAddedCustomer",
            data: {
                _token:"{{ csrf_token() }}",
                adminId: id
            },
            async: true,
            success: function(arrayed_result) {
                if(arrayed_result.length>0){
                    $("#emptyKarbarButton").prop("disabled",false);
                    $("#moveKarbarButton").prop("disabled",false);
                    $("#deleteAdmin").prop("disabled",true);
                }else{
                    $("#emptyKarbarButton").prop("disabled",true);
                    $("#moveKarbarButton").prop("disabled",true);
                    $("#deleteAdmin").prop("disabled",false);
                }
                $('#addedCustomer').empty();
                arrayed_result.forEach((element, index) => {
                    $('#addedCustomer').append(`
                <tr onclick="checkCheckBox(this,event)">
                    <td id="radif" style="width:55px;">` + (index + 1) + `</td>
                    <td id="mCode" style="width:115px;">` + element.PCode +`</td>
                    <td >` + element.Name + `</td>
                    <td style="width:50px;">
                        <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` + element.PSN + `" id="kalaId">
                    </td>
                </tr>
            `);
                });
            },
            error: function(data) {}
        });
    } else {
        $("#emptyKarbarButton").prop("disabled",true);
        $("#moveKarbarButton").prop("disabled",true);
        $("#deleteAdmin").prop("disabled",true);
        $("#customerContainer").css("display", "none");
    }
}
function setAdminListStuff(element,adminType,adminId,logedInId) {
    
    $(element).find('input:radio').prop('checked', true);
    let input = $(element).find('input:radio');
    let id = input.val();
    $("#asn").val(id);
    $("#AdminForAdd").val(id);
    if (adminType == 2) {
        $.ajax({
            method:'get',
            url: baseUrl + "/getAddedCustomer",
            data: {
                _token:"{{ csrf_token() }}",
                adminId: id
            },
            async: true,
            success: function(arrayed_result) {
                if(arrayed_result.length>0){
                    $("#deleteSupporter").prop("disabled",true);
                    $("#setEditStuff").prop("disabled",true);
                    $("#deleteDriver").prop("disabled",true);
                    $("#deleteMarketer").prop("disabled",true);
                    $("#deleteAdmin").prop("disabled",true);
                    $("#editAdmin").prop("disabled",true);
                    $("#editDriver").prop("disabled",true);
                    $("#editSupporter").prop("disabled",false);
                    $("#editMarketer").prop("disabled",true);
                }else{
                    $("#deleteSupporter").prop("disabled",false);
                    $("#editSupporter").prop("disabled",false);
                    $("#deleteMarketer").prop("disabled",true);
                    $("#setEditStuff").prop("disabled",true);
                    $("#deleteDriver").prop("disabled",true);
                    $("#editAdmin").prop("disabled",true);
                    $("#editDriver").prop("disabled",true);
                    $("#editSupporter").prop("disabled",false);
                    $("#editMarketer").prop("disabled",true);
                }
            },
            error: function(data) {}
        });
    } else {
        if(adminType==3){
            $.ajax({
                method:'get',
                url: baseUrl + "/getAddedCustomer",
                data: {
                    _token:"{{ csrf_token() }}",
                    adminId: id
                },
                async: true,
                success: function(arrayed_result) {
                    if(arrayed_result.length>0){
                        $("#deleteMarketer").prop("disabled",true);
                        $("#deleteSupporter").prop("disabled",true);
                        $("#setEditStuff").prop("disabled",true);
                        $("#deleteDriver").prop("disabled",true);
                        $("#deleteAdmin").prop("disabled",true);
                        $("#editAdmin").prop("disabled",true);
                        $("#editDriver").prop("disabled",true);
                        $("#editSupporter").prop("disabled",true);
                        $("#editMarketer").prop("disabled",false);
                    }else{
                        $("#deleteMarketer").prop("disabled",false);
                        $("#deleteSupporter").prop("disabled",true);
                        $("#setEditStuff").prop("disabled",true);
                        $("#deleteDriver").prop("disabled",true);
                        $("#deleteAdmin").prop("disabled",true);
                        $("#editAdmin").prop("disabled",true);
                        $("#editDriver").prop("disabled",true);
                        $("#editSupporter").prop("disabled",true);
                        $("#editMarketer").prop("disabled",false);
                    }
                },
                error: function(data) {}
            });
        }else{
            if(adminType==1){
                $("#deleteMarketer").prop("disabled",true);
                $("#deleteSupporter").prop("disabled",true);
                $("#setEditStuff").prop("disabled",true);
                $("#deleteDriver").prop("disabled",true);
                $("#deleteAdmin").prop("disabled",true);
                $("#editAdmin").prop("disabled",false);
                $("#editDriver").prop("disabled",true);
                $("#editSupporter").prop("disabled",true);
                $("#editMarketer").prop("disabled",true);
            }else{
                if(adminType==5){
                    if(logedInId==adminId){
                        $("#deleteMarketer").prop("disabled",true);
                        $("#deleteAdmin").prop("disabled",true);
                        $("#editAdmin").prop("disabled",false);
                        $("#editDriver").prop("disabled",true);
                        $("#editSupporter").prop("disabled",true);
                        $("#editMarketer").prop("disabled",true);
                        $("#setEditStuff").prop("disabled",true);
                        $("#deleteDriver").prop("disabled",true);
                    }else{
                        $("#deleteMarketer").prop("disabled",true);
                        $("#deleteAdmin").prop("disabled",true);
                        $("#editAdmin").prop("disabled",true);
                        $("#editDriver").prop("disabled",true);
                        $("#editSupporter").prop("disabled",true);
                        $("#editMarketer").prop("disabled",true);
                        $("#setEditStuff").prop("disabled",true);
                        $("#deleteDriver").prop("disabled",true);
                    }
                }else{
                    $("#deleteMarketer").prop("disabled",true);
                    $("#deleteSupporter").prop("disabled",true);
                    $("#deleteAdmin").prop("disabled",true);
                    $("#deleteDriver").prop("disabled",false);
                    $("#editAdmin").prop("disabled",true);
                    $("#editDriver").prop("disabled",false);
                    $("#editSupporter").prop("disabled",true);
                    $("#editMarketer").prop("disabled",true);
                }
            }
        }
    }
}

$("#addMessageButton").on("click",()=>{
    $("#userList").modal("show");
})
function setMessageStuff(element) {
    $(element).find('input:radio').prop('checked', true);
    let input = $(element).find('input:radio');
    let adminType = input.val().split('_')[1];
    let id = input.val().split('_')[0];
    $.ajax({
        method: 'get',
        url: baseUrl + "/getAdminInfo",
        data: {
            _token:"{{ csrf_token() }}",
            id: id
        },
        async: true,
        success: function(msg) {
            $("#sendTo").text(msg[3].name+' '+msg[3].lastName);
            $("#getterId").val(msg[3].id);
            let sended=msg[0];
            let myId=msg[2];
            let appositId=msg[1];
            $("#messageList").empty();
            sended.forEach((element,index)=>{
                if(appositId==element.getterId){
                $("#messageList").append(
                    `<div class="d-flex flex-row justify-content-start mb-1">
                    <img src="resources/assets/images/admins/`+myId+`.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                    <div class="p-2 ms-2" style="border-radius:10px; height:40px; background-color: rgba(78, 192, 229, 0.2);">
                        <p class="small" style="font-size:0.9rem;"> `+element.messageContent+`</p>
                    </div>
                </div>`
                );
                }else{
                    $("#messageList").append(
                        `<div class="d-flex flex-row justify-content-end mb-2">
                        <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; height:40px; background-color: #fbfbfb;">
                        <p class="small" style="font-size:0.9rem;"> `+element.messageContent+`</p>
                        </div>
                        <img src="resources/assets/images/admins/`+appositId+`.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                    </div>`
                    );
                }
            });
            $("#addMessage").modal("show");
            $("#userList").modal("hide");},
    error:function(err) {

    }});
}
$("#addMessageForm").submit(function(e) {
    $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function(arrayed_result) {
            $("#messageContent").val("");
            let sended=arrayed_result[0];
            let myId=arrayed_result[2];
            let appositId=arrayed_result[1];
            $("#messageList").empty();
            sended.forEach((element,index)=>{
                if(appositId==element.getterId){
                $("#messageList").append(
                    `<div class="d-flex flex-row justify-content-start mb-1">
                    <img src="resources/assets/images/admins/`+myId+`.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                    <div class="p-2 ms-2" style="border-radius:10px; height:40px; background-color: rgba(78, 192, 229, 0.2);">
                        <p class="small" style="font-size:0.9rem;"> `+element.messageContent+`</p>
                    </div>
                </div>`
                );
                }else{
                    $("#messageList").append(
                        `<div class="d-flex flex-row justify-content-end mb-2">
                        <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; height:40px; background-color: #fbfbfb;">
                        <p class="small" style="font-size:0.9rem;"> `+element.messageContent+`</p>
                        </div>
                        <img src="resources/assets/images/admins/`+appositId+`.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                    </div>`
                    );
                }
            });
        },
        error:()=>{
            alert('bad');
        }
    });
    e.preventDefault();
});
function setReadMessageStuff(element) {
    $(element).find('input:radio').prop('checked', true);
    let input = $(element).find('input:radio');
    $("#senderId").val(input.val());
    $("#getterIdD").val(input.val());
    sendId=$("#senderId").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/getDiscusstion",
        data: {
            _token: "{{ csrf_token() }}",
            sendId: sendId
        },
        async: true,
        success: function(arrayed_result) {
            let sended=arrayed_result[0];
            let appositId=arrayed_result[1];
            let myId=arrayed_result[2];
            $("#sendedMessages").empty();
            $("#recivedMessages").empty();
            $("#messageDiscusstion").empty();
            sended.forEach((element,index)=>{
                if(appositId==element.getterId){
                $("#messageDiscusstion").append(
                    `<div class="d-flex flex-row justify-content-start mb-1">
                    <img src="resources/assets/images/admins/`+myId+`.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                    <div class="p-2 ms-2" style="border-radius:10px; height:40px; background-color: rgba(78, 192, 229, 0.2);">
                        <p class="small" style="font-size:0.9rem;"> `+element.messageContent+`</p>
                    </div>
                </div>`
                );
                }else{
                    $("#messageDiscusstion").append(
                        `<div class="d-flex flex-row justify-content-end mb-2">
                        <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; height:40px; background-color: #fbfbfb;">
                        <p class="small" style="font-size:0.9rem;"> `+element.messageContent+`</p>
                        </div>
                        <img src="resources/assets/images/admins/`+appositId+`.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                    </div>`
                    );
                }
            });
            $("#readComments").modal("show");
        },
        error: function(data) {}
    });
}

$("#addDisscusstionForm").submit(function(e) {
    $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function(arrayed_result) {
            $("#messageArea").val("");
            let sended=arrayed_result[0];
            let myId=arrayed_result[2];
            let appositId=arrayed_result[1];
            $("#sendedMessages").empty();
            $("#recivedMessages").empty();
            $("#messageDiscusstion").empty();
            sended.forEach((element,index)=>{
                if(appositId==element.getterId){
                $("#messageDiscusstion").append(
                    `<div class="d-flex flex-row justify-content-start mb-1">
                    <img src="resources/assets/images/admins/`+myId+`.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                    <div class="p-2 ms-2" style="border-radius:10px; height:40px; background-color: rgba(78, 192, 229, 0.2);">
                        <p class="small" style="font-size:0.9rem;"> `+element.messageContent+`</p>
                    </div>
                </div>`
                );
                }else{
                    $("#messageDiscusstion").append(
                        `<div class="d-flex flex-row justify-content-end mb-2">
                        <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; height:40px; background-color: #fbfbfb;">
                        <p class="small" style="font-size:0.9rem;"> `+element.messageContent+`</p>
                        </div>
                        <img src="resources/assets/images/admins/`+appositId+`.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                    </div>`
                    );
                }
            });
            $("#readComments").modal("show");
        },
        error:()=>{
            alert('bad');
        }
    });
    e.preventDefault();
});

function checkCheckBox(element, event) {
    if (event.target.type == "checkbox") {
        e.stopPropagation();
    } else {
        if ($(element).find('input:checkbox').prop('disabled') == false) {
            if ($(element).find('input:checkbox').prop('checked') == false) {
                $(element).find('input:checkbox').prop('checked', true);

            } else {
                $(element).find('input:checkbox').prop('checked', false);
                $(element).find('td.selected').removeClass("selected");
            }
        }
    }
}
$(".selectAllFromTop").on("change", (e) => {
    if ($(e.target).is(':checked')) {
        var table = $(e.target).closest('table');
        if (!$('td input:checkbox', table).is(':disabled')) {
            $('td input:checkbox', table).prop('checked', true);
        }
    } else {
        var table = $(e.target).closest('table');
        $('td input:checkbox', table).prop('checked', false);
    }

});

$("#addCustomerToAdmin").on("click", () => {
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید مشتریان اضافه شوند؟',
        icon: 'warning',
        buttons: true
    }).then(function(willAdd) {
        if(willAdd) {

    let adminId = $("#AdminForAdd").val();
    var customerID = [];
    $('input[name="customerIDs[]"]:checked').map(function() {
        customerID.push($(this).val());
    });
    $.ajax({
        method: 'get',
        url: baseUrl + "/AddCustomerToAdmin",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
            customerIDs: customerID
        },
        async: true,
        success: function(arrayed_result) {
            $('#addedCustomer').empty();
            arrayed_result.forEach((element, index) => {
                $('#addedCustomer').append(`
                <tr  onclick="checkCheckBox(this,event)">
                    <td>` + (index + 1) + `</td>
                    <td>` + element.PCode + `</td>
                    <td>` + element.Name + `</td>
                    <td>
                    <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` + element.PSN + `">
                    </td>
                </tr>
            `);
            });
        },
        error: function(data) {}

    });
    $.ajax({
        method: 'get',
        url: baseUrl + "/getCustomer",
        data: {
            _token: "{{ csrf_token() }}"
        },
        async: true,
        success: function(arrayed_result) {
            $('#allCustomer').empty();
            arrayed_result.forEach((element, index) => {
                $('#allCustomer').append(`
            <tr  onclick="checkCheckBox(this,event)">
                <td>` + (index + 1) + `</td>
                <td>` + element.PCode + `</td>
                <td>` + element.Name + `</td>
                <td>
                <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` + element.PSN + `" id="customerId">
                </td>
            </tr>
        `);
            });
        },
        error: function(data) {}
    });
    }else{ 

    }
});
});
$("#removeCustomerFromAdmin").on("click", () => {
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید مشتریان حذف شوند؟',
        icon: 'warning',
        buttons: true
    }).then(function(willAdd) {
        if(willDelete) {
    var customerIDs = [];
    adminId = $("#AdminForAdd").val();
    $('input[name="addedCustomerIDs[]"]:checked').map(function() {
        customerIDs.push($(this).val());
    });
    $.ajax({
        method: 'get',
        url: baseUrl + "/RemoveCustomerFromAdmin",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
            customerIDs: customerIDs
        },
        async: true,
        success: function(arrayed_result) {
            $('#addedCustomer').empty();
            arrayed_result.forEach((element, index) => {
                $('#addedCustomer').append(`
                <tr  onclick="checkCheckBox(this,event)">
                    <td>` + (index + 1) + `</td>
                    <td>` + element.PCode +`</td>
                    <td>` + element.Name + `</td>
                    <td>
                    <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` + element.PSN + `">
                    </td>
                </tr>
            `);
            });
        },
        error: function(data) {}

    });
}
});
});
$("#searchAddedCity").on("change",function(){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchAddedCity").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#searchAddedMantagheh').empty();
            arrayed_result.forEach((element, index) => {
                $('#searchAddedMantagheh').append(`
                <option value="`+element.SnMNM+`">`+element.NameRec+`</option>
            `);
            });
        },
        error: function(data) {}

    });
});
$("#searchByCity").on("change",()=>{
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchByCity").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#searchByMantagheh').empty();
            arrayed_result.forEach((element, index) => {
                $('#searchByMantagheh').append(`
                <option value="`+element.SnMNM+`">`+element.NameRec+`</option>
            `);
            });
        },
        error: function(data) {}

    });
});
$("#searchCity").on("change",function(){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchCity").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#searchMantagheh').empty();
            arrayed_result.forEach((element, index) => {
                $('#searchMantagheh').append(`
                <option value="`+element.SnMNM+`">`+element.NameRec+`</option>
            `);
            });
        },
        error: function(data) {}

    });
});
$("#snNahiyehE").on("change",function(){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#snNahiyehE").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#snMantaghehE').empty();
            arrayed_result.forEach((element, index) => {
                $('#snMantaghehE').append(`
                <option value="`+element.SnMNM+`">`+element.NameRec+`</option>
            `);
            });
        },
        error: function(data) {}

    });
});
$("#findMantaghehByCity").on("change",()=>{
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#findMantaghehByCity").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#searchCustomerByMantagheh').empty();
            $("#searchCustomerByMantagheh").append(`<option value="0">همه</option>`);
            arrayed_result.forEach((element, index) => {
                $('#searchCustomerByMantagheh').append(`
                <option value="`+element.SnMNM+`">`+element.NameRec+`</option>
            `);
            });

        },
        error: function(data) {}

    });
    });

    $("#searchCustomerByMantagheh").on("change",()=>{
        let searchTerm1=$("#searchCustomerByMantagheh").val();
        $("#mantaghehId").val(searchTerm1);
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchCustomerByMantagheh",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm1
            },
            async: true,
            success: function(msg) {
                // $('.crmDataTable').dataTable().fnDestroy();
                $("#customerListBody1").empty();
                msg.forEach((element,index)=>{
                    let backgroundColor="";
                    if(element.maxTime){
                        backgroundColor="lightblue"
                    }
                    $("#customerListBody1").append(`
                    <tr onclick="selectAndHighlight(this)" style="background-color:`+backgroundColor+`">
                    <td>`+(index+1)+`</td>
                    <td>`+element.PCode+`</td>
                    <td>`+element.Name+`</td>
                    <td  class="scrollTd">`+element.peopeladdress+`</td>
                    <td>`+element.sabit+`</td>
                    <td>`+element.hamrah+`</td>
                    <td>`+element.NameRec+`</td>
                    <td>2</td>
                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                    </tr>`);
                });
                // $('.crmDataTable').dataTable();
                // $('.crmDataTable').dataTable({
                //     "pagingType": "full_numbers"
                // });
            },
            error: function(data) {}
        });
    });


$("#searchMantagheh").on("change",function(){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchCustomerByRegion",
        data: {
            _token: "{{ csrf_token() }}",
            rsn: $("#searchMantagheh").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#allCustomer').empty();
            arrayed_result.forEach((element, index) => {
                $('#allCustomer').append(`
            <tr  onclick="checkCheckBox(this,event)">
                <td >` + (index + 1) + `</td>
                <td>` + element.PCode +  `</td>
                <td>` + element.Name + `</td>
                <td>
                <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` + element.PSN + `" id="customerId">
                </td>
            </tr>
        `);
            });
        },
        error: function(data) {}

    });
});

$("#searchAddedMantagheh").on("change",function(){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAddedCustomerByRegion",
        data: {
            _token: "{{ csrf_token() }}",
            rsn: $("#searchAddedMantagheh").val(),
            asn:$("#asn").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#addedCustomer').empty();
            arrayed_result.forEach((element, index) => {
                $('#addedCustomer').append(`
            <tr onclick="checkCheckBox(this,event)">
                <td id="radif">` + (index + 1) + `</td>
                <td id="mCode">` + element.PCode + `</td>
                <td>` + element.Name + `</td>
                <td>
                    <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` + element.PSN + `" id="kalaId">
                </td>
            </tr>
            `);
            });
        },
        error: function(data) {}

    });
});

$("#searchAddedNameByMNM").on("keyup",()=>{
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAddedCustomerByNameMNM",
        data: {
            _token: "{{ csrf_token() }}",
            rsn: $("#searchAddedMantagheh").val(),
            asn:$("#asn").val(),
            name:$("#searchAddedNameByMNM").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#addedCustomer').empty();
            arrayed_result.forEach((element, index) => {
                $('#addedCustomer').append(`
            <tr onclick="checkCheckBox(this,event)">
                <td id="radif">` + (index + 1) + `</td>
                <td id="mCode">` + element.PCode + "first" +`</td>
                <td>` + element.Name + `</td>
                <td>
                    <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` + element.PSN + `" id="kalaId">
                </td>
            </tr>
            `);
            });
        },
        error: function(data) {}

    });
});

$("#searchNameByMNM").on("keyup",()=>{
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchCustomerByNameMNM",
        data: {
            _token: "{{ csrf_token() }}",
            rsn: $("#searchMantagheh").val(),
            name:$("#searchNameByMNM").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#allCustomer').empty();
            arrayed_result.forEach((element, index) => {
                $('#allCustomer').append(`
            <tr  onclick="checkCheckBox(this,event)">
                <td >` + (index + 1) + `</td>
                <td>` + element.PCode + `</td>
                <td>` + element.Name + `</td>
                <td>
                <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` + element.PSN + `" id="customerId">
                </td>
            </tr>
        `);
            });
        },
        error: function(data) {}

    });
});


$("#addCommentForm").submit(function(e) {
    $("#addComment").modal("hide");
    $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function(data) {
            swal({
                title: 'موفق!',
                text: 'ثبت شد!',
                icon: 'success',
                buttons: true
            });
            $("#firstComment").val("");
            $("#secondComment").val("");
            $("#commentDate2").val("");
            moment.locale('en');
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerComments").empty();
            data[0].forEach((element, index) => {
                $("#customerComments").append(`<tr class="tbodyTr">
                    <td> ` + (index + 1) + ` </td>
                    <td>` +moment(element.TimeStamp, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D')+ `</td>
                    <td onclick="viewComment(` + element.id + `)">` + element.newComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i> </td>
                    <td onclick="viewNextComment(` + element.id + `)">` + element.nexComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i>  </td>
                    <td>` + moment(element.specifiedDate, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D') + `</td>
                    </tr>`);
            });
            $("#customerListBody1").empty();
            data[1].forEach((element,index)=>{
                let backgroundColor="";
                if(element.maxTime){
                    backgroundColor="lightblue"
                }
                $("#customerListBody1").append(`
                <tr onclick="selectAndHighlight(this)" style="background-color:`+backgroundColor+`">
                <td>`+(index+1)+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td  class="scrollTd">`+element.peopeladdress+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+element.NameRec+`</td>
                <td>2</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        }
    });
    e.preventDefault();
});
$("#openAddCommentModal").on("click", () => {
    $("#addComment").modal("show");
});
$("#openDashboardForAlarm").on("click", () => {
    let csn = $("#customerSn").val();
    let asn = $("#adminSn").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/customerDashboardForAlarm",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            asn: asn
        },
        async: true,
        success: function(msg) {

            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            let comments = msg[4];
            let specialComment = msg[5];
            let assesments=msg[6];
            if(specialComment[0]){
            let comment = specialComment[0];
            $("#specialComment").text(comment.comment);
            }
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").val(exactCustomer.PCode);
            $("#customerName").val(exactCustomer.Name);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#mobile1").val(exactCustomer.PhoneStr);
            $("#customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").val(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(`
                <tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + element.FactDate + `</td>
                    <td>نامعلوم</td>
                    <td>` + parseInt(element.TotalPriceHDS/10).toLocaleString("en-us") + `</td>
                    <td  onclick="showFactorDetails(this)"><input name="factorId" style="display:none" type="radio" value="` + element.SerialNoHDS + `" /><i class="fa fa-eye" /></td>
                </tr>
                `);
            });

            $('#goodDetail').empty();
            goodDetails.forEach((element, index) => {
                $('#goodDetail').append(`
                <tr>
                <td> ` + (index + 1) + ` </td>
                <td>` + m + `</td>
                <td>` + element.GoodName + `</td>
                </tr >`);
            });
            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(`<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + element.TimeStamp + `</td>
                    <td>` + element.GoodName + `</td>
                    <td>` + element.Amount + `</td>
                    <td>` + element.Fi + `</td>
                    </tr>`);
            });

            $("#customerComments").empty();
            comments.forEach((element, index) => {
                $("#customerComments").append(`<tr>
                    <td> ` + (index + 1) + ` </td>
                    <td>` + element.TimeStamp + `</td>
                    <td  onclick="viewComment(` + element.id + `)"</td>` + element.newComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i> </td>
                    <td  onclick="viewNextComment(` + element.id + `)">` + element.nexComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i>  </td>
                    <td>` + element.specifiedDate + `</td>
                    </tr>`);
            });
            $("#alarmTableAssesment").empty();
            assesments.forEach((element,index)=>{
                let driverBehavior="";
                let shipmentProblem="بله";
                if(element.shipmentProblem==1){
                    shipmentProblem="خیر"
                }
                switch (parseInt(element.driverBehavior)) {
                    case 1:
                        driverBehavior="عالی"
                        break;
                    case 2:
                        driverBehavior="خوب"
                        break;
                    case 3:
                        driverBehavior="متوسط"
                        break;
                    case 4:
                        driverBehavior="بد"
                        break;
                    default:
                        break;
                }
                $("#alarmTableAssesment").append(`
                <tr>
                <td>`+(index+1)+`</td>
                <td>`+element.TimeStamp+`</td>
                <td>`+element.comment+`</td>
                <td>`+driverBehavior+`</td>
                <td>`+shipmentProblem+`</td>
                <td><i class="fa fa-eye"/></td>
                <td><input type="radio" class="form-input"/></td>
            </tr>
                `);
            })
            $("#customerDashboard").modal("show");
        },
        error: function(data) {}
    });
});

function alarmHistory() {
    let factorId = $('#factorAlarm').val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/getAlarmHistory",
        data: {
            _token: "{{ csrf_token() }}",
            fsn: factorId
        },
        async: true,
        success: function(data) {
            $("#alarmHistoryBody").empty();
            data.forEach((element, index) => {
                $("#alarmHistoryBody").append(`
                <tr>
                <td>` + (index + 1) + `</td>
                <td>` + element.alarmDate + `</td>
                <td>` + element.comment + `</td>
            </tr>`);
            });
            $("#alarmHistoryModal").modal("show");
        }
    });

}
$('.select-highlight tr').click(function() {
    $(this).children('td').children('input').prop('checked', true);
    $(".enableBtn").prop("disabled", false);
    if ($(".enableBtn").is(":disabled")) {} else {
        $(".enableBtn").css("color", "red !important");
    }
    $('.select-highlight tr').removeClass('selected');

    $(this).toggleClass('selected');
    $('#customerSn').val($(this).children('td').children('input').val().split('_')[0]);
});

function selectAndHighlight(element) {
    $(element).children('td').children('input').prop('checked', true);
    $(".enableBtn").prop("disabled", false);
    if ($(".enableBtn").is(":disabled")) {} else {
        $(".enableBtn").css("color", "red !important");
    }
    $('.select-highlight tr').removeClass('selected');

    $(element).toggleClass('selected');
    $('#customerSn').val($(element).children('td').children('input').val().split('_')[0]);
}

$("#openCustomerActionModal").on("click", () => {
    let csn = $("#customerSn").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/customerDashboardForAdmin",
        data: {
            _token: "{{csrf_token()}}",
            csn: csn
        },
        async: true,
        success: function(msg) {
            moment.locale('en');
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            let comments = msg[4];
            let specialComments = msg[5];
            let assesments= msg[6];
            let returendFactors= msg[7];
            let specialComment = specialComments[0];
            $("#customerProperty").text(specialComment.comment.trim());
            $("#customerCode").val(exactCustomer.PCode);
            $("#customerName").val(exactCustomer.Name);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#mobile1").val(exactCustomer.hamrah.split("\n")[0]);
            $("#mobile2").val(exactCustomer.hamrah.split("\n")[1]);
            $("#tell").val(exactCustomer.sabit);
            let adminName=(exactCustomer.adminName.trim()+' '+exactCustomer.lastName.trim());
            $("#admin").val(adminName);
            $("#customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").val(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(`
                <tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + element.FactDate + `</td>
                    <td>نامعلوم</td>
                    <td>` + parseInt(element.TotalPriceHDS/10).toLocaleString("en-us") + `</td>
                    <td onclick="showFactorDetails(this)"><input name="factorId" style="display:none"  type="radio" value="` + element.SerialNoHDS + `" /><i class="fa fa-eye" /></td>
                </tr>
                `);
            });

            $("#returnedFactorTable").empty();
            returendFactors.forEach((element, index) => {
                $("#returnedFactorTable").append(`
                <tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + element.FactDate + `</td>
                    <td>نامعلوم</td>
                    <td>` + parseInt(element.TotalPriceHDS/10).toLocaleString("en-us") + `</td>
                </tr>
                `);
            });
            $('#goodDetail').empty();
            goodDetails.forEach((element, index) => {
                $('#goodDetail').append(`<tr>
                <td>` + (index + 1) +` </td>
                <td>` + moment(element.maxTime, 'YYYY/M/D').locale('fa').format('YYYY/M/D')+ `</td>
                <td>` + element.GoodName + `</td>
                <td> </td>
                </tr>`);
            });
            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(`<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + moment(element.TimeStamp, 'YYYY/M/D').locale('fa').format('YYYY/M/D')+ `</td>
                    <td>` + element.GoodName + `</td>
                    <td>` + element.Amount + `</td>
                    <td>` + element.Fi + `</td>
                    </tr>`);
            });
            $("#customerComments").empty();
            comments.forEach((element, index) => {
                $("#customerComments").append(`<tr>
                    <td> ` + (index + 1) + ` </td>
                    <td>` + moment(element.TimeStamp, 'YYYY/M/D').locale('fa').format('YYYY/M/D') + `</td>
                    <td  onclick="viewComment(` + element.id + `)"</td>` + element.newComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i> </td>
                    <td  onclick="viewNextComment(` + element.id + `)">` + element.nexComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i>  </td>
                    <td>` + moment(element.specifiedDate, 'YYYY/M/D').locale('fa').format('YYYY/M/D') + `</td>
                    </tr>`);
            });
            $("#karbarActionAssesment").empty();
            assesments.forEach((element,index)=>{
                let driverBehavior="";
                let shipmentProblem="بله";
                if(element.shipmentProblem==1){
                    shipmentProblem="خیر"
                }
                switch (parseInt(element.driverBehavior)) {
                    case 1:
                        driverBehavior="عالی"
                        break;
                    case 2:
                        driverBehavior="خوب"
                        break;
                    case 3:
                        driverBehavior="متوسط"
                        break;
                    case 4:
                        driverBehavior="بد"
                        break;
                    default:
                        break;
                }
                $("#karbarActionAssesment").append(`
                <tr>
                <td>`+(index+1)+`</td>
                <td>`+moment(element.TimeStamp, 'YYYY/M/D').locale('fa').format('YYYY/M/D')+`</td>
                <td>`+element.comment+`</td>
                <td>`+driverBehavior+`</td>
                <td>`+shipmentProblem+`</td>
                <td><i class="fa fa-eye"/></td>
                <td><input type="radio" class="form-input"/></td>
            </tr>
                `);
            });
            $("#reportCustomerModal").modal("show");
        },
        error: function(data) {}
    });
});

function changeAlarm() {
    let csn = $("#customerSn").val();
    let asn = $("#adminSn").val();
    $("#adminIdForAlarm").val(asn);
    $("#customerIdForAlarm").val(csn);
    $("#changeAlarm").modal("show");
}
$("#changeAlarmForm").submit(function(e) {
    $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function(data) {
            $("#changeAlarm").modal("hide");
            data.forEach((element, index) => {
                $("alarmsbody").append(`<tr>
                <td>` + (index + 1) + `</td>
                <td>` + element.Name + `</td>
                <td>` + element.peopeladdress + `</td>
                <td>` + element.PhoneStr + `</td>
                <td>` + element.PhoneStr + `</td>
                <td></td>
                <td></td>
                <td></td>
                <td>` + element.name + ' ' + element.lastName + `</td>
                <td><input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN + '_' + element.admin_id + '_' + element.SerialNoHDS + `"></td>
            </tr>`);
            });
        }
    });
    e.preventDefault();
});

function assesmentStuff(element) {
    let input = $(element).find('input:radio').prop("checked", true);
    $("#customerSn").val(input.val().split("_")[0]);
    $("#factorSn").val(input.val().split("_")[1]);
    $("#customerIdForAssesment").val(input.val().split("_")[0]);
    $("#factorIdForAssesment").val(input.val().split("_")[1]);
    $("#openAssesmentModal").prop('disabled',false);
    $("#openAssessmentModal1").prop('disabled',false);
}

function checkExistance(element) {
    userName = element.value;
    $.ajax({
        method: 'get',
        url: baseUrl + "/checkUserNameExistance",
        data: {
            _token: "{{ csrf_token() }}",
            username: userName
        },
        async: true,
        success: function(msg) {
            if (msg > 0) {
                $("#existAlert").text("قبلا موجود است");
            }
        },
        error: function(data) {}
    });
}
$("#emptyKarbarButton").on("click", () => {
    let asn = $("#AdminForAdd").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/getAdminForEmpty",
        data: {
            _token: "{{ csrf_token() }}",
            asn: asn
        },
        async: true,
        success: function(msg) {
            let admin = msg[0];
            let adminType = "";
            if (admin.adminType == 1) {
                adminType = "ادمین";
            } else {
                if (admin.adminType == 2) {
                    adminType = "پشتیبان";
                } else {
                    if (admin.adminType == 3) {
                        adminType = "بازاریاب";
                    } else {
                        if (admin.adminType == 4) {
                            adminType = "راننده";
                        }
                    }
                }
            }
            
            let discription = "";
            if (admin.discription != null) {
                discription = admin.discription;
            }
            if (admin.adminType != 1 && admin.adminType != 4 && admin.emptyState != 1) {
                $("#emptyKarbar").empty();
                $("#emptyKarbar").append(`<tr>
                    <td style="font-size:18px; font-weight:bold">` + admin.name + ` ` + admin.lastName + `</td>
                    <td style="font-size:18px; font-weight:bold">` + adminType + `</td>
                    <td>` + discription + `</td>
                    </tr>`);
                    
                $("#removeKarbar").modal("show");
            }
        },
        error: function(data) {}
    });
});

$("#openDashboard").on("click", () => {
    let csn = ($("#customerSn").val()).split(" ")[0];
    $("#customerProperty").val("");
    let commentSn = $("#commentSn").val();
    $("#lastCommentId").val(commentSn);
    $.ajax({
        method: 'get',
        url: baseUrl + "/customerDashboard",
        dataType: 'json',
        contentType: 'json',
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn
        },
        async: true,
        success: function(msg) {
            moment.locale('en');
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            let comments = msg[4];
            let specialComments = msg[5];
            let specialComment = specialComments[0];
            let assesments=msg[6];
            let returnedFactors=msg[7];
            let loginInfo=msg[8];
            if(specialComment){
            $("#customerProperty").val(specialComment.comment.trim());
            }
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").val(exactCustomer.PCode);
            $("#customerName").val(exactCustomer.Name);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#mobile1").val(exactCustomer.hamrah.split("\n")[0]);
            $("#tell").val(exactCustomer.sabit.split("\n")[0]);
            $("#mobile2").val(exactCustomer.hamrah.split("\n")[1]);
            $("#customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").val(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(`<tr class="tbodyTr">
                    <td>` + (index + 1) + `</td>
                    <td>` + element.FactDate+ `</td>
                    <td>نامعلوم</td>
                    <td>` + parseInt(element.TotalPriceHDS/10).toLocaleString("en-us") + `</td>
                    <td onclick="showFactorDetails(this)"><input name="factorId" style="display:none"  type="radio" value="` + element.SerialNoHDS + `" /><i class="fa fa-eye" /></td>
                </tr>`);
            });
           
            $("#returnedFactorsBody").empty();
            returnedFactors.forEach((element, index) => {
                $("#returnedFactorsBody").append(`<tr class="tbodyTr">
                <td>` + (index + 1) + `</td>
                <td>` + element.FactDate+ `</td>
                <td>نامعلوم</td>
                <td>` + parseInt(element.TotalPriceHDS/10).toLocaleString("en-us") + `</td>
                </tr>`);
            });
            $('#goodDetail').empty();
            goodDetails.forEach((element, index) => {
                $('#goodDetail').append(`
                <tr class="tbodyTr">
                    <td>` + (index + 1) + ` </td>
                    <td>` + moment(element.maxTime, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D') + `</td>
                    <td>` + element.GoodName + `</td>
                    <td>  </td>
                    <td>  </td>
                    
                </tr>`);
            });

            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(`<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + moment(element.TimeStamp, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D') + `</td>
                    <td>` + element.GoodName + `</td>
                    <td>` + element.Amount + `</td>
                    <td>` + element.Fi + `</td>
                    </tr>`);
            });
            
            $("#customerLoginInfoBody").empty();
            if(loginInfo){
            loginInfo.forEach((element, index) => {
                $("#customerLoginInfoBody").append(`<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + moment(element.visitDate, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D') + `</td>
                    <td>` + element.platform + `</td>
                    <td>` + element.browser + `</td>
                    </tr>`);
            });
        }

            $("#customerComments").empty();
            comments.forEach((element, index) => {
                $("#customerComments").append(`<tr class="tbodyTr">
                    <td> ` + (index + 1) + ` </td>
                    <td>` +moment(element.TimeStamp, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D')+ `</td>
                    <td onclick="viewComment(` + element.id + `)"</td>` + element.newComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i> </td>
                    <td onclick="viewNextComment(` + element.id + `)">` + element.nexComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i>  </td>
                    <td>` + moment(element.specifiedDate, 'YYYY/M/D').locale('fa').format('YYYY/M/D') + `</td>
                    </tr>`);
            });
            $("#customerAssesments").empty();
            assesments.forEach((element,index)=>{
                let driverBehavior="";
                let shipmentProblem="بله";
                if(element.shipmentProblem==1){
                    shipmentProblem="خیر"
                }
                switch (parseInt(element.driverBehavior)) {
                    case 1:
                        driverBehavior="عالی"
                        break;
                    case 2:
                        driverBehavior="خوب"
                        break;
                    case 3:
                        driverBehavior="متوسط"
                        break;
                    case 4:
                        driverBehavior="بد"
                        break;
                    default:
                        break;
                }
                $("#customerAssesments").append(`
                <tr>
                <td>`+(index+1)+`</td>
                <td>`+moment(element.TimeStamp, 'YYYY/M/D').locale('fa').format('YYYY/M/D')+`</td>
                <td>`+element.comment+`</td>
                <td>`+driverBehavior+`</td>
                <td class="scrollTd">`+shipmentProblem+`</td>
                <td></td>
            </tr>`);
            });
            $("#customerDashboard").modal("show");
        },
        error: function(data) {}
    });
});

function openAssesmentStuff() {
    $("#assesmentDashboard").modal("show");
    $.ajax({
        method: 'get',
        url: baseUrl + "/getFactorDetail",
        data: {
            _token: "{{ csrf_token() }}",
            FactorSn: $("#factorSn").val()
        },
        async: true,
        success: function(msg) {
            let factor = msg[0];
            $("#factorDate").text(factor.FactDate);
            $("#customerNameFactor").text(factor.Name);
            $("#customerComenter").text(factor.Name);
            $("#customerAddressFactor").text(factor.peopeladdress);
            $("#customerPhoneFactor").text(factor.PhoneStr);
            $("#customerPhoneFactor").text(factor.FactNo);
            $("#assesmentDashboard").modal("show");
            $("#productList").empty();
            msg.forEach((element, index) => {
                $("#productList").append(`                                  <tr>
                <td>` + (index + 1) + `</td>
                <td>` + element.GoodName + ` </td>
                <td>` + element.Amount / 1 + `</td>
                <td>` + element.UName + `</td>
                <td>` + (element.Fi / 10).toLocaleString("en-us") + `</td>
                <td>` + (element.goodPrice / 10).toLocaleString("en-us") + `</td>
                </tr>`);
            });
        },
        error: function(data) {}
    });
}
$("#inactiveButton").on("click", () => {
    $("#inactiveId").val($("#customerSn").val());
    $("#inactiveCustomer").modal("show");
});

$("#addAssesment").submit(function(e) {
    $("#assesmentDashboard").modal("hide");
    $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function(data) {
            $("#customersAssesBody").empty();
            data.forEach((element,index)=>{
                $("#customersAssesBody").append(`
                <tr onclick="assesmentStuff(this)">
                <td class="no-sort" style="width:40px">`+(index+1)+`</td>
                <td>`+element.Name+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+(element.TotalPriceHDS/10).toLocaleString('en')+`</td>
                <td style="width:70px">`+element.FactNo+`</td>
                <td style="width:40px"> <input class="customerList form-check-input" name="factorId" type="radio" value="`+element.PSN+`_`+element.SerialNoHDS+`"></td>
            </tr>
                `);
            });
        }
    });
    e.preventDefault();
});

$("#visitorSearchName").on("keyup",()=>{
    let searchTerm=$("#visitorSearchName").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/getCustomerLoginInfo",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {
            $("#listVisitorBody").empty();
            msg.forEach((element,index)=>{
                $("#listVisitorBody").append(`<tr>
                <td style="width:40px">`+(index+1)+`</td>
                <td style="width:60px">`+moment(element.firstVisit, 'YYYY-M-D HH:mm:ss').locale('fa').format('D/M/YYY') +`</td>
                <td style="width:60px">`+moment(element.lastVisit, 'YYYY-M-D HH:mm:ss').locale('fa').format('D/M/YYY') +`</td>
                <td style="width:60px">`+element.Name+`</td>
                <td style="width:60px">`+element.platform+`</td>
                <td style="width:60px">`+element.browser+`</td>
                <td style="width:60px">`+element.countLogin+`</td>
                </tr>`);
            });
        },
        error: function(data) {alert("bad");}
    });
});

$("#openCommentTimeTable").on("click", () => {
    $("#addComment").modal("show");
});
$("#addCommentTimeTable").submit(function(e) {
    $("#addComment").modal("hide");
    $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function(data) {
            swal({
                title: 'موفق!',
                text: 'ثبت شد!',
                icon: 'success',
                buttons: true
            });
            $.ajax({
                method: 'get',
                url: baseUrl + "/getCustomerForTimeTable",
                data: {
                    _token: "{{ csrf_token() }}",
                    dayDate: $("#dayDate").val()
                },
                async: true,
                success: function(msg) {
                    if(msg.length>0){
                    $("#customerListSection").css({"display":"block"});
                    }else{
                    $("#customerListSection").css({"display":"none"}); 
                    }
                    // $('.crmDataTable').dataTable().fnDestroy();
                    $("#customerListBody").empty();
                    msg.forEach((element, index) => {
                        $("#customerListBody").append(`
                        <tr  onclick="timeTableCustomerStuff(this)">
                            <td>` + (index + 1) + `</td>
                            <td>` + element.PCode + `</td>
                            <td>` + element.Name + `</td>
                            <td  class="scrollTd">` + element.peopeladdress + `</td>
                            <td>` + element.sabit + `</td>
                            <td>` + element.hamrah + `</td>
                            <td>` + element.NameRec + `</td>
                            <td> <input name="timeTableCustomer" class="form-check-input" type="radio" value="` + element.PSN + `_` + element.commentId + `"></td>
                        </tr>`);
                    });
                    // $('.crmDataTable').dataTable();
                },
                error: function(data) {}
            });
        }
    });
    e.preventDefault();
    let csn = $("#customerSn").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/customerDashboard",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn
        },
        async: true,
        success: function(msg) {
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").val(exactCustomer.PCode);
            $("#customerName").val(exactCustomer.Name);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#mobile1").val(exactCustomer.PhoneStr);
            $("#customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").val(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(`
                <tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + element.FactDate + `</td>
                    <td>نامعلوم</td>
                    <td>` + parseInt(element.TotalPriceHDS/10).toLocaleString("en-us") + `</td>
                    <td onclick="showFactorDetails(this)"><span><input name="factorId" style="display:none"  type="radio" value="` + element.SerialNoHDS + `" /><i class="fa fa-eye" /></span></td>
                </tr>
                `);
            });
            $('#goodDetail').empty();
            goodDetails.forEach((element, index) => {
                $('#goodDetail').append(`
                <tr>
                <td> ` + (index + 1) + ` </td>
                <td>` + element.TimeStamp + `</td>
                <td>` + element.GoodName + `</td>
                <td>` + element.Amount + `</td>
                <td>` + element.Fi + `</td>

                </tr >`);
            });
            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(`<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + element.TimeStamp + `</td>
                    <td>` + element.GoodName + `</td>
                    <td>` + element.Amount + `</td>
                    <td>` + element.Fi + `</td>
                    </tr>`);
            });
            $("#customerDashboard").modal("show");
        },
        error: function(data) {}
    });
});

function setAdminStuffForMove(element) {
    $(element).find('input:radio').prop('checked', true);
    let input = $(element).find('input:radio');
    let adminId = input.val();
    $("#adminID").val(adminId);
    alert(adminId);
}



function refreshDashboard() {
    $("#addComment").modal("hide");
    let csn = $("#customerSn").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/customerDashboard",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn
        },
        async: true,
        success: function(msg) {
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").val(exactCustomer.PCode);
            $("#customerName").val(exactCustomer.Name);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#mobile1").val(exactCustomer.PhoneStr);
            $("#customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").val(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(`
                <tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + element.FactDate + `</td>
                    <td>نامعلوم</td>
                    <td>` + parseInt(element.TotalPriceHDS/10).toLocaleString("en-us") + `</td>
                    <td onclick="showFactorDetails(this)"><span><input name="factorId" style="display:none"  type="radio" value="` + element.SerialNoHDS + `" /><i class="fa fa-eye" /></span></td>
                </tr>
                `);
            });
            $('#goodDetail').empty();
            goodDetails.forEach((element, index) => {
                $('#goodDetail').append(`
                <tr>
                <td>` + (index + 1) + ` </td>
                <td>` + element.TimeStamp + `</td>
                <td>` + element.GoodName + `</td>
                <td>` + element.Amount + `</td>
                <td>` + element.Fi + `</td>
                </tr >`);
            });
            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(`<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + element.TimeStamp + `</td>
                    <td>` + element.GoodName + `</td>
                    <td>` + element.Amount + `</td>
                    <td>` + element.Fi + `</td>
                    </tr>`);
            });
            $("#customerDashboard").modal("show");
        },
        error: function(data) {}
    });
}

function viewComment(id) {
    let comment;
    $.ajax({
        method: 'get',
        url: baseUrl + "/getFirstComment",
        data: {
            _token: "{{ csrf_token() }}",
            commentId: id
        },
        async: true,
        success: function(msg) {
            comment = msg.newComment;
            $("#readCustomerComment1").text(comment);
            $("#viewComment").modal("show");
        },
        error: function(data) {}
    });
}

function viewNextComment(id) {
    let comment;
    $.ajax({
        method: 'get',
        url: baseUrl + "/getFirstComment",
        data: {
            _token: "{{ csrf_token() }}",
            commentId: id
        },
        async: true,
        success: function(msg) {
            comment = msg.nexComment;
            $("#readCustomerComment1").text(comment);
            $("#viewComment").modal("show");
        },
        error: function(data) {}
    });
}

$('#viewComment').blur(function() {
    $("#viewComment").modal("hide");
    $("#readCustomerComment1").empty();
});

function showTimeTableTasks(element) {
    
    let input = $(element).find('input:radio');
    $("#dayDate").val(input.val());
    $.ajax({
        method: 'get',
        url: baseUrl + "/getCustomerForTimeTable",
        data: {
            _token: "{{ csrf_token() }}",
            dayDate: input.val()
        },
        async: true,
        success: function(msg) {
            if(msg.length>0){
            $("#customerListSection").css({"display":"block"});
            }else{
            $("#customerListSection").css({"display":"none"}); 
            }
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody").empty();
            msg.forEach((element, index) => {
                $("#customerListBody").append(`
                <tr  onclick="timeTableCustomerStuff(this)">
                    <td>` + (index + 1) + `</td>
                    <td>` + element.PCode + `</td>
                    <td>` + element.Name + `</td>
                    <td  class="scrollTd">` + element.peopeladdress + `</td>
                    <td>` + element.sabit + `</td>
                    <td>` + element.hamrah + `</td>
                    <td>` + element.NameRec + `</td>
                    <td> <input name="timeTableCustomer" class="form-check-input" type="radio" value="` + element.PSN + `_` + element.commentId + `"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        },
        error: function(data) {}
    });
}

function timeTableCustomerStuff(element) {
    let input = $(element).find('input:radio').prop("checked", true);
    $("#customerSn").val(input.val().split("_")[0]);
    $("#commentSn").val(input.val().split("_")[1]);
    $(".enableBtn").prop("disabled",false);
}

function showAssesComment(id) {
    $.ajax({
        method: 'get',
        url: baseUrl + "/getAssesComment",
        data: {
            _token: "{{ csrf_token() }}",
            assesId: id
        },
        async: true,
        success: function(msg) {
            $("#assesComment").text(msg.comment);
            $("#readAssesComment").modal("show");
        },
        error: function(data) {}
    });
}

function returnedCustomerStuff(element) {
    let input = $(element).find('input:radio').prop("checked", true);
    $("#customerSn").val(input.val().split("_")[0]);
    $("#adminSn").val(input.val().split("_")[1]);
    $(".enableBtn").prop("disabled", false);
}

$("#returnCustomer").on("click", () => {
    let csn = $("#customerSn").val();
    $("#returnCustomerId").val(csn);
    $("#returnComment").modal("show");
});
$("#cancelSetAlarm").on("click",()=>{
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید بدون ذخیره خارج شوید؟',
        icon: 'warning',
        buttons: true
    }).then(function(value) {
        if(value === true) {
        $("#changeAlarm").modal('hide');
        }else{
            $("#changeAlarm").modal('show');  
        }
    });
});

$("#cancelEditCustomer").on("click",()=>{
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید بدون ذخیره خارج شوید؟',
        icon: 'warning',
        buttons: true
    }).then(function(value) {
        if(value === true) {
        $("#editNewCustomer").modal('hide');
        }else{
            $("#editNewCustomer").modal('show');  
        }
    });
});

$("#cancelinActive").on("click",()=>{
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید بدون ذخیره خارج شوید؟',
        icon: 'warning',
        buttons: true
    }).then(function(value) {
        if(value === true) {
        $("#inactiveCustomer").modal('hide');
        }else{
            $("#inactiveCustomer").modal('show');  
        }
    });
}); 

$("#cancelTakhsis").on("click",()=>{
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید بدون ثبت تخصیص خارج شوید؟',
        icon: 'warning',
        buttons: true
    }).then(function(value) {
        if(value === true) {
        $("#takhsesKarbar").modal('hide');
        }else{
            $("#takhsesKarbar").modal('show');  
        }
    });
}); 

$("#cancelCommentButton").on("click",()=>{
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید بدون ذخیره خارج شوید؟',
        icon: 'warning',
        buttons: true
    }).then(function(value) {
        if(value === true) {
        $("#addComment").modal('hide');
        }else{
            $("#addComment").modal('show');  
        }
    });
}); 

$("#cancelReturn").on("click",()=>{
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید بدون ذخیره خارج شوید؟',
        icon: 'warning',
        buttons: true
    }).then(function(value) {
        if(value === true) {
        $("#returnComment").modal('hide');
        }else{
            $("#returnComment").modal('show');  
        }
    });
});

$("#cancelInActive").on("click",()=>{
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید بدون ذخیره خارج شوید؟',
        icon: 'warning',
        buttons: true
    }).then(function(value) {
        if(value === true) {
        $("#inactiveCustomer").modal('hide');
        }else{
            $("#inactiveCustomer").modal('show');  
        }
    });
});


$("#cancelAssesment").on("click",()=>{
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید بدون ذخیره خارج شوید؟',
        icon: 'warning',
        buttons: true
    }).then(function(value) {
        if(value === true) {
        $("#assesmentDashboard").modal('hide');
        }else{
            $("#assesmentDashboard").modal('show');  
        }
    });
});

$('#returnCustomerForm').submit(function(e) {
    $("#returnComment").modal("hide");
    $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function(data) {
            swal({
                title: 'موفق!',
                text: 'ثبت شد!',
                icon: 'success',
                buttons: true
            });
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            data.forEach((element, index) => {
                let backgroundColor="";
                if(element.countComment>0){
                    backgroundColor="lightblue"
                }
                $("#customerListBody1").append(`
                <tr onclick="selectAndHighlight(this)" style="background-color:`+backgroundColor+`">
                <td>`+(index+1)+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td  class="scrollTd">`+element.peopeladdress+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+element.NameRec+`</td>
                <td>2</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        }
    });
    e.preventDefault();
});

$("#openDashboardAlarm").on("click", () => {
    $("#karbarAlarm").modal("show");
})

function takhsisCustomer() {
    $("#takhsesKarbar").modal("hide");
    let csn = $("#customerSn").val();
    let FirstAdminID = $("#adminSn").val();
    let asn = $("input[name='AdminId']:checked").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/takhsisCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            asn: asn,
            FirstAdminID: FirstAdminID
        },
        async: true,
        success: function(msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#returnedCustomerList").empty();
            msg.forEach((element, index) => {
                $("#returnedCustomerList").append(`
            <tr onclick="returnedCustomerStuff(this)">
            <td>` + (index + 1) + `</td>
            <td>` + element.Name + `</td>
            <td>` + element.PCode + `</td>
            <td>` + element.peopeladdress + `</td>
            <td>` + element.PhoneStr + `</td>
            <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` + element.PSN + `_` + element.adminId + `"></td>
        </tr>`);
            });
            // $('.crmDataTable').dataTable();
            
        },
        error: function(data) {}
    });
}
function openEditCustomerModalForm() {
    let csn = $("#customerSn").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/getCustomerInfo",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn
        },
        async: true,
        success: function(respond) {
            let exactCustomerInfo=respond[0];
            let phones=respond[1];
            let cities=respond[2];
            let mantagheh=respond[3];

            $("#customerID").val(exactCustomerInfo.PSN);
            $("#name").val(exactCustomerInfo.Name);
            $("#PCode").val(exactCustomerInfo.PCode);
            $("#mobilePhone").val(phones[0].hamrah);
            $("#sabitPhone").val(phones[0].sabit);
            $("#gender").empty();
            $("#gender").append(`
                <option value="2" >مرد</option>
                <option value="1" >زن</option>`);
            $("#snNahiyehE").empty();
            cities.forEach((element,index)=>{
                let selectRec="";
                if(element.SnMNM==exactCustomerInfo.SnNahiyeh){
                    selectRec="selected";
                }
            $("#snNahiyehE").append(
            `<option value="`+element.SnMNM+`" `+selectRec+`>`+element.NameRec+`</option>`);
            });

            $("#snMantaghehE").empty();
            mantagheh.forEach((element,index)=>{
                let selectRec="";
                if(element.SnMNM==exactCustomerInfo.SnMantagheh){
                    selectRec="selected";
                }
            $("#snMantaghehE").append(
            `<option value="`+element.SnMNM+`" `+selectRec+`>`+element.NameRec+`</option>`);
            });
            $("#peopeladdress").val(exactCustomerInfo.peopeladdress);
            $("#password").val(exactCustomerInfo.customerPss);
            $("#editNewCustomer").modal("show");
        },
        error: function(data) {}
    });
    
}
function takhsisNewCustomer() {
    $("#takhsesKarbar").modal("hide");
    let csn = $("#customerSn").val();
    let asn = $("input[name='AdminId']:checked").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/takhsisNewCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            asn: asn
        },
        async: true,
        success: function(msg) {
            swal("موفقانه اختصاص داده شد.", {
                icon: "success",
            });
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element, index) => {
                $("#customerListBody1").append(`
                <tr>
                <td style="width:40px">`+index+1+`</td>
                <td>`+element.Name+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.NameRec+`</td>
                <td>`+moment(element.TimeStamp,'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>
                <td>`+element.peopeladdress+`</td>
                <td>`+element.adminName+` `+element.adminLastName+`</td>
                <td style="width:40px"> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+` `+element.GroupCode+`"></td>
            </tr>`);
            });
            // $('.crmDataTable').dataTable();
            
        },
        error: function(data) {}
    });
}
// function takhsisNewCustomer() {
//     $("#takhsesKarbar").modal("hide");
//     let csn = $("#customerSn").val();
//     let FirstAdminID = $("#adminSn").val();
//     let asn = $("input[name='AdminId']:checked").val();
//     $.ajax({
//         method: 'get',
//         url: baseUrl + "/takhsisCustomerFromEmpty",
//         data: {
//             _token: "{{ csrf_token() }}",
//             csn: csn,
//             asn: asn,
//             FirstAdminID: FirstAdminID
//         },
//         async: true,
//         success: function(msg) {
//             // $('.crmDataTable').dataTable().fnDestroy();
//             $("#returnedCustomerList").empty();
//             msg.forEach((element, index) => {
//                 $("#returnedCustomerList").append(`
//             <tr onclick="returnedCustomerStuff(this)">
//             <td>` + (index + 1) + `</td>
//             <td>` + element.Name + `</td>
//             <td>` + element.PCode + `</td>
//             <td>` + element.peopeladdress + `</td>
//             <td>` + element.PhoneStr + `</td>
//             <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` + element.PSN + `_` + element.adminId + `"></td>
//         </tr>`);
//             });
//             // $('.crmDataTable').dataTable();
            
//         },
//         error: function(data) {}
//     });
// }

function activateCustomer() {
    let csn = $("#customerSn").val();
    let asn = $("input[name='AdminId']:checked").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/activateCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            asn: asn
        },
        async: true,
        success: function(msg) {
            $("#inactiveCustomerBody").empty();
            msg.forEach((element, index) => {
                $("#inactiveCustomerBody").append(`
            <tr onclick="setInActiveCustomerStuff(this)">
            <td>` + (index + 1) + `</td>
            <td>` + element.Name + `</td>
            <td>` + element.PCode + `</td>
            <td>` + element.peopeladdress + `</td>
            <td>` + element.PhoneStr + `</td>
            <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` + element.PSN + `"></td>
        </tr>`);
            });
            $("#takhsesKarbar").modal("hide");
        },
        error: function(data) {}
    });
}

$("#takhsisButton").on("click", () => {
    $("#inactiveId").val($("#customerSn").val());
    $("#takhsesKarbar").modal("show");
});

function setInActiveCustomerStuff(element) {
    let input = $(element).find('input:radio').prop("checked", true);
    $("#customerSn").val(input.val());
}

$("#inactiveCustomerForm").submit(function(e) {
    swal({
        title: "مطمئین هستید؟",
        text: "پس از غیر فعالسازی این مشتری به لیست غیر فعالها اضافه می شود. !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#inactiveCustomer").modal("hide");
            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(msg) {
                    // $('.crmDataTable').dataTable().fnDestroy();
                    $("#returnedCustomerList").empty();
                    msg.forEach((element, index) => {
                        $("#returnedCustomerList").append(`
                                                <tr onclick="returnedCustomerStuff(this)">
                                                <td>` + (index + 1) + `</td>
                                                <td>` + element.Name + `</td>
                                                <td>` + element.PCode + `</td>
                                                <td>` + element.peopeladdress + `</td>
                                                <td>` + element.hamrah + `</td>
                                                <td>` + element.adminName + `` + element.adminLastName + `</td>
                                                <td>`+moment(element.returnDate, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>

                                                <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` + element.PSN + `_` + element.adminId + `"></td>
                                                </tr>`);
                    });
                    // $('.crmDataTable').dataTable();
                    swal("مشتری غیر فعال شد", {
                        icon: "success",
                    });
                }
            });
        }
    });
    e.preventDefault();
});


function removeStaff() {
    swal({
        title: "مطمئین هستید؟",
        text: "پس از تخلیه نمی توانید این مشتریان را برگردانید !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#removeKarbar").modal("hide");
            $.ajax({
                method: 'get',
                url: baseUrl + "/emptyAdmin",
                data: {
                    _token: "{{ csrf_token() }}",
                    asn: $("#AdminForAdd").val()
                },
                async: true,
                success: function(msg) {
                    if (msg == 1) {
                        swal("مشتریان تخلیه گردید", {
                            icon: "success",
                        });
                    } else {}
                },
                error: function(data) {}
            });
        }
    });
}

function moveStaff() {
    swal({
        title: "مطمئین هستید؟",
        text: "پس از انتقال نمی توانید این مشتریان را برگردانید !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#moveKarbar").modal("hide");
            $.ajax({
                method: 'get',
                url: baseUrl + "/moveCustomerToAdmin",
                data: {
                    _token: "{{ csrf_token() }}",
                    holderID: $("#adminID").val(),
                    giverID: $("#adminTakerId").val()
                },
                async: true,
                success: function(msg) {
                    if (msg == 1) {
                        swal("مشتریان انتقال گردید", {
                            icon: "success",
                        });
                        
                    } else {}
                },
                error: function(data) {}
            });
        }
    });
}


$("#moveKarbarButton").on("click", () => {
    let asn = $("#AdminForAdd").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/getAdminForMove",
        data: {
            _token: "{{ csrf_token() }}",
            asn: asn
        },
        async: true,
        success: function(msg) {
            let adminArray = msg[0];
            let admin = adminArray[0];
            let otherAdmins = msg[1];
            let adminType = "";
            let discription = "توضیحی ندارد.";
            if (admin.discription != null) {
                discription = admin.discription;
            }
            if (admin.adminType == 1) {
                adminType = "ادمین";
            } else {
                if (admin.adminType == 2) {
                    adminType = "پشتیبان";
                } else {
                    if (admin.adminType == 3) {
                        adminType = "بازاریاب";
                    } else {
                        if (admin.adminType == 4) {
                            adminType = "راننده";
                        }
                    }
                }
            }
            if (admin.adminType != 1 && admin.adminType != 4 && admin.emptyState != 1) {
                $("#adminToMove").empty();
                $("#adminToMove").append(`<tr>
                    <td style="font-size:18px; font-weight:bold">` + admin.name + ` ` + admin.lastName + `</td>
                    <td style="font-size:18px; font-weight:bold">` + adminType + `</td>
                    <td>` + discription + `</td>
                    </tr>`);
                $("#moveKarbar").modal("show");
            }
            $("#selectKarbarToMove").empty();
            otherAdmins.forEach((element, index) => {
                adminType = "پشتیبان";
                discription = "توضیحی ندارد";
                if (element.discription != null) {
                    discription = element.discription;
                }
                switch (element.adminType) {
                    case 2:
                        adminType = "پشتیبان";
                        break;
                    case 3:
                        adminType = "بازاریاب";
                        break;
                }
                $("#selectKarbarToMove").append(`
                <tr onclick="setAdminStuffForMove(this)">
                <td>` + (index + 1) + `</td>
                <td>` + element.name + " " + element.lastName + `</td>
                <td>` + adminType + `</td>
                <td>` + discription + `</td>
                <td>
                    <input class="form-check-input" name="adminId" type="radio" value="` + element.id + `">
                </td>
            </tr>`);
            });
        },
        error: function(data) {}
    });

});

$("#cancelAddAddmin").on("click",()=>{
    swal({
        title: "اخطار!",
        text: "می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
if(willDelete){
    $("#newAdmin").modal("hide");
}else{
    $("#newAdmin").modal("show");   
}
    });
});

$("#cancelEditProfile").on("click",()=>{
    swal({
        title: "اخطار!",
        text: "می خواهید بدون ویرایش خارج شوید؟",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
if(willDelete){

    $("#editProfile").modal("hide");

}else{

    $("#editProfile").modal("show");  

}
    });

});

$("#cancelRemoveKarbar").on("click",()=>{
    swal({
        title: "اخطار!",
        text: "می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
if(willDelete){

    $("#removeKarbar").modal("hide");

}else{

    $("#removeKarbar").modal("show");  

}
    });
});

$("#cancelMoveKarbar").on("click",()=>{
    swal({
        title: "اخطار!",
        text: "می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
if(willDelete){

    $("#moveKarbar").modal("hide");

}else{

    $("#moveKarbar").modal("show");  

}
    });
});

function setKarbarEditStuff() {
    let asn = $("#asn").val();
    let admyTypes;
    let sexes;
    $("#editAdminID").val(asn);
    $.ajax({
        method: 'get',
        url: baseUrl + "/getAdminForMove",
        data: {
            _token: "{{ csrf_token() }}",
            asn: asn
        },
        async: true,
        success: function(msg) {
            let adminArray = msg[0];
            let admin = adminArray[0];
            let otherAdmins = msg[1];
            let adminType = "";
            let discription = "توضیحی ندارد.";
            if (admin.discription != null) {
                discription = admin.discription;
            }
            if (admin.adminType == 1) {
                admyTypes = [
                    `<option selected value="1">ادمین</option>`,
                    `<option value="2">پشتیبان</option>`,
                    `<option value="3">بازاریاب</option>`,
                    `<option value="4">راننده</option>`
                ];
            } else {
                if (admin.adminType == 2) {
                    admyTypes = [
                        `<option  value="1">ادمین</option>`,
                        `<option selected value="2">پشتیبان</option>`,
                        `<option value="3">بازاریاب</option>`,
                        `<option value="4">راننده</option>`
                    ];
                } else {
                    if (admin.adminType == 3) {
                        admyTypes = [
                            `<option  value="1">ادمین</option>`,
                            `<option  value="2">پشتیبان</option>`,
                            `<option  selected value="3">بازاریاب</option>`,
                            `<option value="4">راننده</option>`
                        ];
                    } else {
                        if (admin.adminType == 4) {
                            admyTypes = [
                                `<option  value="1">ادمین</option>`,
                                `<option  value="2">پشتیبان</option>`,
                                `<option  value="3">بازاریاب</option>`,
                                `<option selected value="4">راننده</option>`
                            ];
                        }else{
                            admyTypes = [
                                `<option  value="1">ادمین</option>`,
                                `<option  value="2">پشتیبان</option>`,
                                `<option  value="3">بازاریاب</option>`,
                                `<option selected value="4">راننده</option>`,
                                `<option selected value="5">سوپر ادمین</option>`
                            ];
                        }
                    }
                }
            }

            if (admin.sex == 1) {
                sexes = [
                    `<option selected value="1">مرد</option>`,
                    `<option value="2">زن</option>`
                ];
            } else {
                if (admin.sex == 2) {
                    sexes = [
                        `<option value="1">مرد</option>`,
                        `<option selected value="2">زن</option>`
                    ];
                } else {
                    sexes = [
                        `<option value="1">مرد</option>`,
                        `<option value="2">زن</option>`
                    ];
                }
            }

            if (admin.hasAsses == "on") {
                $("#adminHasAssess").prop('checked', true);
            } else {
                $("#adminHasAssess").prop('checked', false);
            }
            if (admin.hasAllCustomer == "on") {
                $("#hasAllCustomer").prop('checked', true);
            } else {
                $("#hasAllCustomer").prop('checked', false);
            }
            $("#adminName").val(admin.name.trim());
            $("#adminLastName").val(admin.lastName.trim());
            $("#adminUserName").val(admin.username.trim());
            $("#adminPassword").val(admin.password.trim());
            $("#adminPhone").val(parseInt(admin.phone.trim()));
            $("#adminDiscription").text(admin.discription.trim());
            $("#adminAddress").val(admin.address.trim());
            $("#adminSex").empty();
            sexes.forEach(element => {
                $("#adminSex").append(element);
            });
            $("#editAdminType").empty();
            admyTypes.forEach(element => {
                $("#editAdminType").append(element);
            });

            $("#editAdminID").val(admin.id);
            $("#editProfile").modal("show");

        },
        error: function(data) {}
    });

}

$("#searchCity").on("change",()=>{
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchCity").val()
        },
        async: true,
        success: function(arrayed_result) {
            $('#searchMantagheh').empty();
            arrayed_result.forEach((element, index) => {
                $('#searchMantagheh').append(`
                <option value="`+element.SnMNM+`">`+element.NameRec+`</option>
            `);
            });
        },
        error: function(data) {}

    });
})

$("#customerMap").on("click", () => {
    $("#driverLocation").modal("show");
    let fsn = $("#factorSn").val();
    $.ajax({
        method: "GET",
        url: baseUrl + "/searchMapByFactor",
        data: {
            _token: "{{ csrf_token() }}",
            fsn: fsn
        },
        async: true,

    }).then(function(data) {
        var map = L.map('map2').setView([35.70163, 51.39211], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '<a href="https://osm.org/copyright">CRM</a>'
        }).addTo(map);
        var marker = {};
        data.forEach(function(item) {

            if (item.LatPers > 0 && item.LonPers > 0) {
                var popup = new L.popup().setContent();
                marker = L.marker([item.LonPers, item.LatPers]).addTo(map).bindPopup(popup);

                let btn = document.createElement('a');
                       btn.setAttribute('data-lat', item.LatPers);
                       btn.setAttribute('data-lng', item.LonPers);
                       btn.setAttribute('class', 'map-btn');
                       btn.setAttribute('target', '_blank');
                       btn.setAttribute('href',"https://maps.google.com/?q=" + item.LonPers +","+ item.LatPers);
                       btn.textContent = 'مشتری';
                        marker.bindPopup(btn, {
                            maxWidth: 'auto'
                        });

            }
        });
    });
});


$(window).load(function() {
    var currentUrl = window.location.pathname;
    if (currentUrl == '\/crmDriver') {
        document.querySelector(".affairs").style.display = "none";
        document.querySelector("#publicMenu").style.display = "none";
        $(".topMenu").css({"marginTop":"-44px"});
    }
});

$("#deleteAdmin").on("click", () => {
    if ($("#AdminForAdd").val() > 0) {
        swal({
            title: "مطمئین هستید؟",
            text: "کاربر با تمام جزءیاتش حذف خواهد شد.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: 'get',
                    url: baseUrl + "/deleteAdmin",
                    data: {
                        _token: "{{ csrf_token() }}",
                        asn: $("#AdminForAdd").val()
                    },
                    async: true,
                    success: function(msg) {
                        $("#adminGroupList").empty();
                            msg.forEach((element, index) => {
                            let discription = "";
                            if (element.discription != null) {
                                discription = element.discription;
                            }
                            $("#adminGroupList").append(`
                                <tr onclick="setAdminStuff(this)">
                                <td>` + (index + 1) + `</td>
                                <td>` + element.name + ` ` + element.lastName + `</td>
                                <td>` + element.adminType + `</td>
                                <td>` + discription + `</td>
                                <td>
                                    <input class="mainGroupId" type="radio" name="AdminId[]" value="` + element.id + `_` + element.adminTypeId + `">
                                </td>
                                </tr>`);
                            });
                        swal("کاربر حذف شد.", {
                            icon: "success",
                        });
                        $("#removeKarbar").modal("hide");
                    },
                    error: function(data) {}
                });
            }
        });
    }
});

function deleteAdminList() {
    if ($("#asn").val() > 0) {
        swal({
            title: "مطمئین هستید؟",
            text: "کاربر با تمام جزءیاتش حذف خواهد شد.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: 'get',
                    url: baseUrl + "/deleteAdmin",
                    data: {
                        _token: "{{ csrf_token() }}",
                        asn: $("#asn").val()
                    },
                    async: true,
                    success: function(msg) {
                        $("#adminGroupList").empty();
                        msg.forEach((element, index) => {
                            let discription = "";
                            if (element.discription != null) {
                                discription = element.discription;
                            }
                            $("#adminGroupList").append(`
                                <tr onclick="setAdminStuff(this)">
                                <td>` + (index + 1) + `</td>
                                <td>` + element.name + ` ` + element.lastName + `</td>
                                <td>` + element.adminType + `</td>
                                <td>` + discription + `</td>
                                <td>
                                    <input class="mainGroupId" type="radio" name="AdminId[]" value="` + element.id + `_` + element.adminTypeId + `">
                                </td>
                                </tr>`);
                        });
                        swal("کاربر حذف شد.", {
                            icon: "success",
                        });
                        $("#removeKarbar").modal("hide");

                    },
                    error: function(data) {}
                });
            }
        });
    }
}

function saveCustomerCommentProperty(element) {
    let csn = $("#customerSn").val();
    let comment = element.value;
    $.ajax({
        method: 'get',
        url: baseUrl + "/setCommentProperty",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            comment: comment
        },
        async: true,
        success: function(msg) {
            element.value = "";
            element.value = msg[0].comment;
        },
        error: function(data) {}
    });

}


$("#altime").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "0h:0m:0s YYYY/0M/0D",
    startDate: "today",
    endDate:"1440/5/5"
});
$("#firstDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D"
});
$("#secondDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
    onSelect:()=>{
        let secondDate=$("#secondDate").val();
        let firstDate=$("#firstDate").val();
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchPastAssesByDate",
            data: {
                _token: "{{ csrf_token() }}",
                secondDate: secondDate,
                firstDate:firstDate
            },
            async: true,
            success: function(msg) {
                // $('.crmDataTable').dataTable().fnDestroy();
                $("#customerListBody1").empty();
                msg.forEach((element,index)=>{
                    $("#customerListBody1").append(`
                    <tr onclick="selectAndHighlight(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td class="scrollTd">`+element.peopeladdress+`</td>
                    <td>`+element.sabit+`</td>
                    <td>`+element.hamrah+`</td>
                    <td>`+element.FactNo+`</td>
                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                    </tr>`);
                });
                // $('.crmDataTable').dataTable();
            },
            error: function(data) {}
        });
    }
});

$("#firstDateDoneComment").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D"
});
$("#secondDateDoneComment").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
    onSelect:()=>{
        let secondDate=$("#secondDateDoneComment").val();
        let firstDate=$("#firstDateDoneComment").val();
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchDoneAssesByDate",
            data: {
                _token: "{{ csrf_token() }}",
                secondDate: secondDate,
                firstDate:firstDate
            },
            async: true,
            success: function(msg) {
                moment.locale('en');
                $("#customerListBodyDone").empty();
                msg.forEach((element,index)=>{
                    $("#customerListBodyDone").append(`
                    <tr>
                        <td>`+(index+1)+`</td>
                        <td>`+element.Name+`</td>
                        <td>`+element.PhoneStr+`</td>
                        <td>`+moment(element.TimeStamp, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D') +`</td>
                        <td data-bs-toggle="modal" data-bs-target="#owdati">`+element.comment+` <i class="fas fa-comment-dots float-end"> </i></td>
                        <td data-bs-toggle="modal" data-bs-target="#owdati"> <i class="fas fa-dolly-flatbed "> </i></td>
                    </tr> `);
                });
            },
            error: function(data) { alert("bad")
        }
        });
    }
});

$("#searchEmptyName").on("keyup",()=>{
    let searchTerm=$("#searchEmptyName").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchEmptyByName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {

            // $('.crmDataTable').dataTable().fnDestroy();
                $("#returnedCustomerList").empty();
                msg.forEach((element,index)=>{
                    $("#returnedCustomerList").append(`
                    <tr onclick="returnedCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.PCode+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.PhoneStr+`</td>
                    <td>`+moment(element.removedDate, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>
                    <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+element.PSN+`"></td>
                </tr>`);
                });
                // $('.crmDataTable').dataTable();
        },
        error: function(data) {}
    });
});

$("#searchEmptyPCode").on("keyup",()=>{
    let searchTerm=$("#searchEmptyPCode").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchEmptyByPCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {

            // $('.crmDataTable').dataTable().fnDestroy();
                $("#returnedCustomerList").empty();
                msg.forEach((element,index)=>{
                    $("#returnedCustomerList").append(`
                    <tr onclick="returnedCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.PCode+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.PhoneStr+`</td>
                    <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+element.PSN+`"></td>
                </tr>`);
                });
                // $('.crmDataTable').dataTable();
        },
        error: function(data) {}
    });
});

 $("#searchAllName").on("keyup",()=>{
let searchTerm=$("#searchAllName").val();
$.ajax({
    method: 'get',
    url: baseUrl + "/searchAllCustomerByName",
    data: {
        _token: "{{ csrf_token() }}",
        searchTerm: searchTerm
    },
    async: true,
    success: function(msg) {
        $("#allCustomerReportyBody").empty();
        msg.forEach((element,index)=>{
            $("#allCustomerReportyBody").append(`
            <tr  onclick="setAlarmCustomerStuff(this)">
            <td>`+(index+1)+`</td>
            <td>`+element.Name+`</td>
            <td>`+element.hamrah+` `+element.sabit+`</td>
            <td>`+element.peopeladdress+`</td>
            <td>`+element.countFactor+`</td>
            <td>`+element.lastDate+`</td>
            <td>هنوز نیست</td>
            <td style="width:60px">`+element.adminName+` `+element.lastName+`</td>
            <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
        </tr>`);
        });
    },
    error: function(data) {}
});
 });

 $("#searchByAdmin").on("change",()=>{
    let searchTerm=$("#searchByAdmin").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAllCustomerByAdmin",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {
                $("#allCustomerReportyBody").empty();
                msg.forEach((element,index)=>{
                    $("#allCustomerReportyBody").append(`
                    <tr  onclick="setAlarmCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.hamrah+` `+element.sabit+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.countFactor+`</td>
                    <td>`+element.lastDate+`</td>
                    <td>هنوز نیست</td>
                    <td style="width:60px">`+element.adminName+` `+element.lastName+`</td>
                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
                </tr>`);
                });
        },
        error: function(data) {}
    });
});

 $("#searchAllPCode").on("keyup",()=>{
let searchTerm=$("#searchAllPCode").val();
$.ajax({
    method: 'get',
    url: baseUrl + "/searchAllCustomerByPCode",
    data: {
        _token: "{{ csrf_token() }}",
        searchTerm: searchTerm
    },
    async: true,
    success: function(msg) {
        msg.forEach((element,index)=>{
            $("#allCustomerReportyBody").append(`
            <tr  onclick="setAlarmCustomerStuff(this)">
            <td>`+(index+1)+`</td>
            <td>`+element.Name+`</td>
            <td>`+element.hamrah+` `+element.sabit+`</td>
            <td>`+element.peopeladdress+`</td>
            <td>`+element.countFactor+`</td>
            <td>`+element.lastDate+`</td>
            <td>هنوز نیست</td>
            <td style="width:60px">`+element.adminName+` `+element.lastName+`</td>
            <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
        </tr>`);
        });
    },
    error: function(data) {}
});
 });

$("#searchAllActiveOrNot").on("change",()=>{
let searchTerm=$("#searchAllActiveOrNot").val();
if(searchTerm!=0){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAllCustomerActiveOrNot",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {
            $("#allCustomerReportyBody").empty();
            msg.forEach((element,index)=>{
                $("#allCustomerReportyBody").append(`
                <tr  onclick="setAlarmCustomerStuff(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.Name+`</td>
                <td>`+element.hamrah+` `+element.sabit+`</td>
                <td>`+element.peopeladdress+`</td>
                <td>`+element.countFactor+`</td>
                <td>`+element.lastDate+`</td>
                <td>هنوز نیست</td>
                <td style="width:60px">`+element.adminName+` `+element.lastName+`</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
            </tr>`);
            });
        },
        error: function(data) {}
    });
}
 });

 $("#searchByMantagheh").on("change",()=>{
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAllCustomerByMantagheh",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: $("#searchByMantagheh").val()
        },
        async: true,
        success: function(msg) {
            $("#allCustomerReportyBody").empty();
            msg.forEach((element,index)=>{
                $("#allCustomerReportyBody").append(`
                <tr  onclick="setAlarmCustomerStuff(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.Name+`</td>
                <td>`+element.hamrah+` `+element.sabit+`</td>
                <td>`+element.peopeladdress+`</td>
                <td>`+element.countFactor+`</td>
                <td>`+element.lastDate+`</td>
                <td>هنوز نیست</td>
                <td style="width:60px">`+element.adminName+` `+element.lastName+`</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
            </tr>`);
            });
        },
        error: function(data) {}
    });
});

 $("#locationOrNot").on("change",()=>{
    let searchTerm=$("#locationOrNot").val();
    if(searchTerm!=0){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchAllCustomerLocationOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#allCustomerReportyBody").empty();
                msg.forEach((element,index)=>{
                    $("#allCustomerReportyBody").append(`
                    <tr  onclick="setAlarmCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.hamrah+` `+element.sabit+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.countFactor+`</td>
                    <td>`+element.lastDate+`</td>
                    <td>هنوز نیست</td>
                    <td style="width:60px">`+element.adminName+` `+element.lastName+`</td>
                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
                </tr>`);
                });
            },
            error: function(data) {}
        });
    }
 });

 $("#searchAllFactorOrNot").on("change",()=>{
    let searchTerm=$("#searchAllFactorOrNot").val();
    if(searchTerm>0){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchAllCustomerFactorOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#allCustomerReportyBody").empty();
                msg.forEach((element,index)=>{
                    $("#allCustomerReportyBody").append(`
                    <tr  onclick="setAlarmCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.hamrah+` `+element.sabit+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.countFactor+`</td>
                    <td>`+element.lastDate+`</td>
                    <td>هنوز نیست</td>
                    <td style="width:60px">`+element.adminName+` `+element.lastName+`</td>
                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
                </tr>`);
                });
            },
            error: function(data) {}
        });
    }
 });

 $("#searchAllBasketOrNot").on("change",()=>{
    let searchTerm=$("#searchAllBasketOrNot").val();
    if(searchTerm>0){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchAllCustomerBasketOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#allCustomerReportyBody").empty();
                msg.forEach((element,index)=>{
                    $("#allCustomerReportyBody").append(`
                    <tr  onclick="setAlarmCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.hamrah+` `+element.sabit+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.countFactor+`</td>
                    <td>`+element.lastDate+`</td>
                    <td>هنوز نیست</td>
                    <td style="width:60px">`+element.adminName+` `+element.lastName+`</td>
                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
                </tr>`);
                });
            },
            error: function(data) {}
        });
    }
 });

 $("#searchAllLoginOrNot").on("change",()=>{
    let searchTerm=$("#searchAllLoginOrNot").val();
    if(searchTerm>0){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchAllCustomerLoginOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#allCustomerReportyBody").empty();
                msg.forEach((element,index)=>{
                    $("#allCustomerReportyBody").append(`
                    <tr  onclick="setAlarmCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.hamrah+` `+element.sabit+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.countFactor+`</td>
                    <td>`+element.lastDate+`</td>
                    <td>هنوز نیست</td>
                    <td style="width:60px">`+element.adminName+` `+element.lastName+`</td>
                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
                </tr>`);
                });
            },
            error: function(data) {}
        });
    }
 })

 $("#searchKalaNameCode").on("keyup",()=>{
let searchTerm=$("#searchKalaNameCode").val();
$.ajax({
    method: 'get',
    url: baseUrl + "/searchKalaNameCode",
    data: {
        _token: "{{ csrf_token() }}",
        searchTerm: searchTerm
    },
    async: true,
    success: function(msg) {
            $("#kalaContainer").empty();
            msg.forEach((element,index)=>{
                $("#kalaContainer").append(`
                <tr>
                <td>`+(index+1)+`</td>
                <td>`+element.GoodCde+`</td>
                <td>`+element.GoodName+`</td>
                <td>`+element.title+`</td>
                <td>`+element.maxFactDate+`</td>
                <td>`+element.hideKala+`</td>
                <td style="color:red;background-color:azure">`+element.Amount+`</td>
                <td>
                    <input class="kala form-check-input" name="kalaId[]" type="radio" value="`+element.GoodSn+`" id="flexCheckCheckedKala">
                </td>
            </tr>`);
            });
    },
    error: function(data) {}
});
 })
 $("#searchKalaStock").on("change",()=>{
    let searchTerm=$("#searchKalaStock").val();
    if(searchTerm>0){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchKalaByStock",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {
                $("#kalaContainer").empty();
                msg.forEach((element,index)=>{
                    $("#kalaContainer").append(`
                    <tr>
                    <td>`+(index+1)+`</td>
                    <td>`+element.GoodCde+`</td>
                    <td>`+element.GoodName+`</td>
                    <td>`+element.maxFactDate+`</td>
                    <td>`+element.hideKala+`</td>
                    <td style="color:red;background-color:azure">`+element.Amount+`</td>
                    <td>
                        <input class="kala form-check-input" name="kalaId[]" type="radio" value="`+element.GoodSn+`" id="flexCheckCheckedKala">
                    </td>
                </tr>`);
                });
        },
        error: function(data) {}
    });
}
})
$("#searchKalaActiveOrNot").on("change",()=>{
    let searchTerm=$("#searchKalaActiveOrNot").val();
    if(searchTerm>0){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchKalaByActiveOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                    $("#kalaContainer").empty();
                    msg.forEach((element,index)=>{
                        $("#kalaContainer").append(`
                        <tr>
                        <td>`+(index+1)+`</td>
                        <td>`+element.GoodCde+`</td>
                        <td>`+element.GoodName+`</td>
                        <td>`+element.maxFactDate+`</td>
                        <td>`+element.hideKala+`</td>
                        <td style="color:red;background-color:azure">`+element.Amount+`</td>
                        <td>
                            <input class="kala form-check-input" name="kalaId[]" type="radio" value="`+element.GoodSn+`" id="flexCheckCheckedKala">
                        </td>
                    </tr>`);
                    });
            },
            error: function(data) {}
        });
    }
})
$("#searchKalaExistInStock").on("change",()=>{
    let searchTerm=$("#searchKalaExistInStock").val();
    if(searchTerm>0){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchKalaByZeroOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                    $("#kalaContainer").empty();
                    msg.forEach((element,index)=>{
                        $("#kalaContainer").append(`
                        <tr>
                        <td>`+(index+1)+`</td>
                        <td>`+element.GoodCde+`</td>
                        <td>`+element.GoodName+`</td>
                        <td>`+element.maxFactDate+`</td>
                        <td>`+element.hideKala+`</td>
                        <td style="color:red;background-color:azure">`+element.Amount+`</td>
                        <td>
                            <input class="kala form-check-input" name="kalaId[]" type="radio" value="`+element.GoodSn+`" id="flexCheckCheckedKala">
                        </td>
                    </tr>`);
                    });
            },
            error: function(data) {}
        });
    }
})
$("#searchMainGroupKala").on("change",()=>{
    let searchTerm=$("#searchMainGroupKala").val();
    if(searchTerm>0){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchSubGroupKala",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                    $("#searchSubGroupKala").empty();
                    $("#searchSubGroupKala").append(`
                    <option value="0" selected>همه</option>
                    `);
                    msg.forEach((element,index)=>{
                        $("#searchSubGroupKala").append(`
                        <option value="`+element.id+`">`+element.title+`</option>
                        `);
                    });
            },
            error: function(data) {alert("not GOOD");}
        });
    }else{
        $("#searchSubGroupKala").empty();
        $("#searchSubGroupKala").append(`
        <option value="-1" selected>--</option>
        `);
    }
})
$("#searchSubGroupKala").on("change",()=>{
    let searchTerm=$("#searchSubGroupKala").val();
    if(searchTerm>0){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchBySubGroupKala",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                    $("#kalaContainer").empty();
                    msg.forEach((element,index)=>{
                        $("#kalaContainer").append(`
                        <tr>
                        <td>`+(index+1)+`</td>
                        <td>`+element.GoodCde+`</td>
                        <td>`+element.GoodName+`</td>
                        <td>`+element.title+`</td>
                        <td>`+element.maxFactDate+`</td>
                        <td>`+element.hideKala+`</td>
                        <td style="color:red;background-color:azure">`+element.Amount+`</td>
                        <td>
                            <input class="kala form-check-input" name="kalaId[]" type="radio" value="`+element.GoodSn+`" id="flexCheckCheckedKala">
                        </td>
                    </tr>`);
                    });
            },
            error: function(data) {}
        });
    }
});
$("#searchAdminNameCode").on("keyup",()=>{
    let searchTerm=$("#searchAdminNameCode").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAdminByNameCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {
            
                $("#adminList").empty();
                msg.forEach((element,index)=>{
                    
                    let adminType="";

                    if(element.adminType==3){

                        adminType="بازاریاب";

                    }else{
                          
                        adminType="پشتیبان";  
                    }

                    $("#adminList").append(`
                                        <tr onclick="setAdminStuffForAdmin(this)">
                                        <td>`+(index+1)+`</td>
                                        <td>`+element.name+` `+element.lastName+`</td>
                                        <td>`+adminType+`</td>
                                        <td></td>
                                        <td>
                                            <input class="mainGroupId" type="radio" name="AdminId[]" value="`+element.id+`_`+element.adminTypeId+`">
                                        </td>
                                        </tr>`);
                });
        },
        error: function(data) {}
    });
});

$("#searchAdminGroup").on("change",()=>{
    let searchTerm=$("#searchAdminGroup").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchAdminByType",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                    $("#adminList").empty();
                    msg.forEach((element,index)=>{

                        $("#adminList").append(`
                        <tr onclick="setAdminStuffForAdmin(this)">
                        <td>`+(index+1)+`</td>
                        <td>`+element.name+` `+element.lastName+`</td>
                        <td>`+element.adminType+`</td>
                        <td></td>
                        <td>
                            <input class="mainGroupId" type="radio" name="AdminId[]" value="`+element.id+`_`+element.adminTypeId+`">
                        </td>
                    </tr>`);
                    });
            },
            error: function(data) {alert("not good");}
        });
    }
});

$("#searchAdminActiveOrNot").on("change",()=>{
    let searchTerm=$("#searchAdminActiveOrNot").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchAdminByActivation",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                    $("#adminList").empty();
                    msg.forEach((element,index)=>{

                        $("#adminList").append(`
                        <tr onclick="setAdminStuffForAdmin(this)">
                        <td>`+(index+1)+`</td>
                        <td>`+element.name+` `+element.lastName+`</td>
                        <td>`+element.adminType+`</td>
                        <td></td>
                        <td>
                            <input class="mainGroupId" type="radio" name="AdminId[]" value="`+element.id+`_`+element.adminTypeId+`">
                        </td>
                    </tr>`);
                    });
            },
            error: function(data) {alert("not good");}
        });
    }
});

$("#searchAdminFactorOrNot").on("change",()=>{
    let searchTerm=$("#searchAdminFactorOrNot").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchAdminFactorOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                    $("#adminList").empty();
                    msg.forEach((element,index)=>{
                        
                        $("#adminList").append(`
                        <tr onclick="setAdminStuffForAdmin(this)">
                        <td>`+(index+1)+`</td>
                        <td>`+element.name+` `+element.lastName+`</td>
                        <td>`+element.adminType+`</td>
                        <td></td>
                        <td>
                            <input class="mainGroupId" type="radio" name="AdminId[]" value="`+element.id+`_`+element.adminTypeId+`">
                        </td>
                    </tr>`);
                    });
            },
            error: function(data) {alert("not good");}
        });
    }
});

$("#searchAdminLoginOrNot").on("change",()=>{
    let searchTerm=$("#searchAdminLoginOrNot").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchAdminLoginOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                    $("#adminList").empty();
                    msg.forEach((element,index)=>{
                        

                        $("#adminList").append(`
                        <tr onclick="setAdminStuffForAdmin(this)">
                        <td>`+(index+1)+`</td>
                        <td>`+element.name+` `+element.lastName+`</td>
                        <td>`+element.adminType+`</td>
                        <td>`+element.discription+`</td>
                        <td>
                            <input class="mainGroupId" type="radio" name="AdminId[]" value="`+element.id+`_`+element.adminTypeId+`">
                        </td>
                    </tr>`);
                    });
            },
            error: function(data) {alert("not good");}
        });
    }
});

$("#searchAdminCustomerLoginOrNot").on("change",()=>{
    let searchTerm=$("#searchAdminCustomerLoginOrNot").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchAdminCustomerLoginOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#adminList").empty();
                msg.forEach((element,index)=>{

                    $("#adminList").append(`
                    <tr onclick="setAdminStuffForAdmin(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.name+` `+element.lastName+`</td>
                    <td>`+element.adminType+`</td>
                    <td>`+element.discription+`</td>
                    <td>
                        <input class="mainGroupId" type="radio" name="AdminId[]" value="`+element.id+`_`+element.adminTypeId+`">
                    </td>
                </tr>`);
                });
            },
            error: function(data) {alert("not good");}
        });
    }
});

$("#searchInActiveByName").on("keyup",()=>{
    let searchTerm=$("#searchInActiveByName").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchInActiveCustomerByName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {
            $("#inactiveCustomerBody").empty();
            msg.forEach((element,index)=>{
                $("#inactiveCustomerBody").append(`
                <tr onclick="setInActiveCustomerStuff(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.CustomerName+`</td>
                <td>`+element.PhoneStr+`</td>
                <td>`+moment(element.TimeStamp, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>
                <td>`+element.name+` `+element.lastName+`</td>
                <td>بدست نیامده</td>
                <td>`+element.comment+`</td>
                <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
            </tr>`);
            });
        },
        error: function(data) {alert("not good");}
    });
})

$("#searchInActiveByCode").on("keyup",()=>{
    let searchTerm=$("#searchInActiveByCode").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchInActiveCustomerByCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {
            $("#inactiveCustomerBody").empty();
            msg.forEach((element,index)=>{
                $("#inactiveCustomerBody").append(`
                <tr onclick="setInActiveCustomerStuff(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.CustomerName+`</td>
                <td>`+element.PhoneStr+`</td>
                <td>`+moment(element.TimeStamp, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>
                <td>`+element.name+` `+element.lastName+`</td>
                <td>بدست نیامده</td>
                <td>`+element.comment+`</td>
                <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
            </tr>`);
            });
        },
        error: function(data) {alert("not good");}
    });
})

$("#searchInActiveByLocation").on("change",()=>{
    let searchTerm=$("#searchInActiveByLocation").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchInActiveCustomerByLocation",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#inactiveCustomerBody").empty();
                msg.forEach((element,index)=>{
                    $("#inactiveCustomerBody").append(`
                    <tr onclick="setInActiveCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.CustomerName+`</td>
                    <td>`+element.PhoneStr+`</td>
                    <td>`+moment(element.TimeStamp, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>
                    <td>`+element.name+` `+element.lastName+`</td>
                    <td>بدست نیامده</td>
                    <td>`+element.comment+`</td>
                    <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
                </tr>`);
                });
            },
            error: function(data) {alert("not good");}
        });
    }
})
$("#orderInactiveCustomers").on("change",()=>{
    let searchTerm=$("#orderInactiveCustomers").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/orderInactiveCustomers",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#inactiveCustomerBody").empty();
                msg.forEach((element,index)=>{
                    $("#inactiveCustomerBody").append(`
                    <tr onclick="setInActiveCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.CustomerName+`</td>
                    <td>`+element.PhoneStr+`</td>
                    <td>`+moment(element.TimeStamp, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>
                    <td>`+element.name+` `+element.lastName+`</td>
                    <td>بدست نیامده</td>
                    <td>`+element.comment+`</td>
                    <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`"></td>
                </tr>`);
                });
            },
            error: function(data) {alert("not good");}
        });
    }
})

$("#searchByReturner").on("change",()=>{
    let searchTerm=$("#searchByReturner").val();
    if(searchTerm !=0){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchByReturner",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {

            // $('.crmDataTable').dataTable().fnDestroy();
                moment.locale('en');
                $("#returnedCustomerList").empty();
                msg.forEach((element,index)=>{
                    $("#returnedCustomerList").append(`
                    <tr onclick="returnedCustomerStuff(this)">
                        <td>`+(index+1)+`</td>
                        <td>`+element.Name+`</td>
                        <td>`+element.PCode+`</td>
                        <td class="scrollTd">`+element.peopeladdress+`</td>
                        <td>`+element.hamrah+`</td>
                        <td>`+element.adminName+` `+element.adminLastName+`</td>
                        <td>`+moment(element.returnDate, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>
                        <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+element.PSN+` `+element.adminId+`"></td>
                    </tr> `);
                });
                // $('.crmDataTable').dataTable();
        },
        error: function(data) {}
    });
}
});

$("#commentDate").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "0h:0m:0s YYYY/0M/0D",
    startDate: "today",
    endDate:"1440/5/5"
});

$("#commentDate2").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
    startDate: "today",
    endDate:"1440/5/5"
});

$("#commentDate3").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
    startDate: "today",
    endDate:"1440/5/5"
});
$("#LoginDate2").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "YYYY/0M/0D"
});

$("#commentDate1").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
    startDate: "today",
    endDate:"1440/5/5"
});

$("#searchAllCName").on("keyup",function(){
    let searchTerm1=$("#searchAllCName").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAllCustomerByName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1
        },
        async: true,
        success: function(msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element,index)=>{
                $("#customerListBody1").append(`
                <tr onclick="selectAndHighlight(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td  class="scrollTd">`+element.peopeladdress+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+element.NameRec+`</td>
                <td>2</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        },
        error: function(data) {}
    });
});

$("#searchAllCCode").on("keyup",function(){
    let searchTerm1=$("#searchAllCCode").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchAllCustomerByCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1
        },
        async: true,
        success: function(msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element,index)=>{
                $("#customerListBody1").append(`
                <tr onclick="selectAndHighlight(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td  class="scrollTd">`+element.peopeladdress+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+element.NameRec+`</td>
                <td>2</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        },
        error: function(data) {}
    });
});

$("#orderAllByCName").on("change",function(){
    let searchTerm1=$("#orderAllByCName").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/orderAllCustomerByCName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1
        },
        async: true,
        success: function(msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element,index)=>{
                $("#customerListBody1").append(`
                <tr onclick="selectAndHighlight(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td  class="scrollTd">`+element.peopeladdress+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+element.NameRec+`</td>
                <td>2</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        },
        error: function(data) {}
    });
});

$("#searchCustomerName").on("keyup",function(){
    let searchTerm1=$("#searchCustomerName").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchCustomerByName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1
        },
        async: true,
        success: function(msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element,index)=>{
                let backgroundColor="";
                if(element.countComment>0){
                    backgroundColor="lightblue"
                }
                $("#customerListBody1").append(`
                <tr onclick="selectAndHighlight(this)" style="background-color:`+backgroundColor+`">
                <td>`+(index+1)+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td  class="scrollTd">`+element.peopeladdress+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+element.NameRec+`</td>
                <td>2</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        },
        error: function(data) {}
    });
});
$("#searchReferedName").on("keyup",()=>{
let searchTerm1=$("#searchReferedName").val();
if(searchTerm1.length>0){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchReferedCustomerName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1
        },
        async: true,
        success: function(msg) {

            // $('.crmDataTable').dataTable().fnDestroy();
            $("#returnedCustomerList").empty();
            msg.forEach((element,index)=>{
                $("#returnedCustomerList").append(`
                <tr  onclick="returnedCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.PCode+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.hamrah+`</td>
                    <td>`+element.adminName+` `+element.adminLastName+`</td>
                    <td>`+element.returnDate+`</td>
                    <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+element.PSN+`_`+element.adminId+`"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        },
        error: function(data) {alert("bad");}
    });
}
});

$("#searchPCode").on("keyup",()=>{
    let searchTerm1=$("#searchPCode").val();
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchReferedPCode",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm1
            },
            async: true,
            success: function(msg) {

                // $('.crmDataTable').dataTable().fnDestroy();
                $("#returnedCustomerList").empty();
                msg.forEach((element,index)=>{
                    $("#returnedCustomerList").append(`
                    <tr onclick="returnedCustomerStuff(this)">
                        <td>`+(index+1)+`</td>
                        <td>`+element.Name+`</td>
                        <td>`+element.PCode+`</td>
                        <td>`+element.peopeladdress+`</td>
                        <td>`+element.PhoneStr+`</td>
                        <td>`+element.adminName+` `+element.adminLastName+`</td>
                        <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+element.PSN+`_`+element.adminId+`"></td>
                    </tr>`);
                });
                // $('.crmDataTable').dataTable();
            },
            error: function(data) {}
        });
    });
$("#searchCustomerCode").on("keyup",function(){
    let searchTerm1=$("#searchCustomerCode").val();
    if(searchTerm1.length>0){
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchCustomerByCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1
        },
        async: true,
        success: function(msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element,index)=>{
                let backgroundColor="";
                if(element.countComment>0){
                    backgroundColor="lightblue"
                }
                $("#customerListBody1").append(`
                <tr onclick="selectAndHighlight(this)" style="background-color:`+backgroundColor+`">
                <td>`+(index+1)+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td  class="scrollTd">`+element.peopeladdress+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+element.NameRec+`</td>
                <td>2</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        },
        error: function(data) {}
    });
}
});

$("#orderByCodeOrName").on("change",()=>{
    let searchTerm1=$("#orderByCodeOrName").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/orderByNameCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1
        },
        async: true,
        success: function(msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element,index)=>{
                let backgroundColor="";
                if(element.countComment>0){
                    backgroundColor="lightblue"
                }
                $("#customerListBody1").append(`
                <tr onclick="selectAndHighlight(this)" style="background-color:`+backgroundColor+`">
                <td>`+(index+1)+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td  class="scrollTd">`+element.peopeladdress+`</td>
                <td>`+element.sabit+`</td>
                <td>`+element.hamrah+`</td>
                <td>`+element.NameRec+`</td>
                <td>2</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.GroupCode+`"></td>
                </tr>`);
            });
            // $('.crmDataTable').dataTable();
        },
        error: function(data) {
            alert("bad");
        }
    });
});
function setAlarmCustomerStuff(element) {
    $(element).children('input').prop('checked', true);
    $(".enableBtn").prop("disabled",false);
    if($(".enableBtn").is(":disabled")){
    }else{
       $(".enableBtn").css("color","red !important");
    }
       $('.select-highlight tr').removeClass('selected');
       $(element).toggleClass('selected');
       $('#customerSn').val($(element).children('td').children('input').val().split('_')[0]);
       $('#adminSn').val($(element).children('td').children('input').val().split('_')[1]);
       $('#factorAlarm').val($(element).children('td').children('input').val().split('_')[2]);
}
$("#searchCustomerAalarmName").on("keyup",()=>{
    let searchTerm=$("#searchCustomerAalarmName").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchCustomerAalarmName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {
            $("#alarmsbody").empty();
            msg.forEach((element,index)=>{
                $("#alarmsbody").append(`
                <tr onclick="setAlarmCustomerStuff(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.Name+`</td>
                <td>`+element.peopeladdress+`</td>
                <td>`+element.hamrah+` `+element.sabit+`</td>
                <td>`+element.NameRec+`</td>
                <td>`+element.assignedDays+`</td>
                <td>`+element.PassedDays+`</td>
                <td>`+element.AdminName+` `+element.lastName+`</td>
                <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.adminId+`_`+element.SerialNoHDS+`"></td>
            </tr>
            `);
            });
        },
        error: function(data) {
            alert("bad");
        }
    });
});

$("#searchCustomerAalarmCode").on("keyup",()=>{
    let searchTerm=$("#searchCustomerAalarmCode").val();
    $.ajax({
        method: 'get',
        url: baseUrl + "/searchCustomerAalarmCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm
        },
        async: true,
        success: function(msg) {
            $("#alarmsbody").empty();
            msg.forEach((element,index)=>{
                $("#alarmsbody").append(`
                <tr  onclick="setAlarmCustomerStuff(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.Name+`</td>
                <td>`+element.peopeladdress+`</td>
                <td>`+element.hamrah+` `+element.sabit+`</td>
                <td>`+element.NameRec+`</td>
                <td>`+element.assignedDays+`</td>
                <td>`+element.PassedDays+`</td>
                <td>`+element.AdminName+` `+element.lastName+`</td>
                <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.adminId+`_`+element.SerialNoHDS+`"></td>
            </tr>
            `);
            });
        },
        error: function(data) {
            alert("bad");
        }
    });
});

$("#searchCustomerAaramActive").on("change",()=>{
    let searchTerm=$("#searchCustomerAaramActive").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchCustomerAalarmActive",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#alarmsbody").empty();
                msg.forEach((element,index)=>{
                    $("#alarmsbody").append(`
                    <tr  onclick="setAlarmCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.hamrah+` `+element.sabit+`</td>
                    <td>`+element.NameRec+`</td>
                    <td>`+element.assignedDays+`</td>
                    <td>`+element.PassedDays+`</td>
                    <td>`+element.AdminName+` `+element.lastName+`</td>
                    <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.adminId+`_`+element.SerialNoHDS+`"></td>
                </tr>
                `);
                });
            },
            error: function(data) {
                alert("bad");
            }
        });
    }
});

$("#searchCustomerAaramLocation").on("change",()=>{
    let searchTerm=$("#searchCustomerAaramLocation").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchCustomerAalarmLocation",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#alarmsbody").empty();
                msg.forEach((element,index)=>{
                    $("#alarmsbody").append(`
                    <tr  onclick="setAlarmCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.hamrah+` `+element.sabit+`</td>
                    <td>`+element.NameRec+`</td>
                    <td>`+element.assignedDays+`</td>
                    <td>`+element.PassedDays+`</td>
                    <td>`+element.AdminName+` `+element.lastName+`</td>
                    <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.adminId+`_`+element.SerialNoHDS+`"></td>
                </tr>
                `);
                });
            },
            error: function(data) {
                alert("bad");
            }
        });
    }
});

$("#searchCustomerAaramOrder").on("change",()=>{
    let searchTerm=$("#searchCustomerAaramOrder").val();
    if(searchTerm>-1){
        $.ajax({
            method: 'get',
            url: baseUrl + "/searchCustomerAalarmOrder",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm
            },
            async: true,
            success: function(msg) {
                $("#alarmsbody").empty();
                msg.forEach((element,index)=>{
                    $("#alarmsbody").append(`
                    <tr  onclick="setAlarmCustomerStuff(this)">
                    <td>`+(index+1)+`</td>
                    <td>`+element.Name+`</td>
                    <td>`+element.peopeladdress+`</td>
                    <td>`+element.hamrah+` `+element.sabit+`</td>
                    <td>`+element.NameRec+`</td>
                    <td>`+element.assignedDays+`</td>
                    <td>`+element.PassedDays+`</td>
                    <td>`+element.AdminName+` `+element.lastName+`</td>
                    <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+element.PSN+`_`+element.adminId+`_`+element.SerialNoHDS+`"></td>
                </tr>
                `);
                });
            },
            error: function(data) {
                alert("bad");
            }
        });
    }
});



  function calcAndClock() {
    var watch = document.querySelector(".affairs");
    var calc = document.querySelector("#myCalculator");
    if (watch.style.display === "none") {
      watch.style.display = "block";
    } else {
        watch.style.display = "none";
    }

    if (calc.style.display === "block") {
      calc.style.display = "none";
    } else {
        calc.style.display = "block";
    }

  }

  function clockAndClac(){
    var calculator = document.querySelector(".crmCalculator");
    var clock = document.querySelector("#myWatch");
    if (calculator.style.display === "none") {
        calculator.style.display = "block";
      } else {
          calculator.style.display = "none";
      }

      if (clock.style.display === "block") {
        clock.style.display = "none";
      } else {
          clock.style.display = "block";
      }
  }

var cancelButton = $('#cancelComment');
cancelButton.on('click', function() {
    swal({
        title: 'اخطار!',
        text: 'آیا می خواهید بدون ثبت کامنت خارج شوید؟',
        icon: 'warning',
        buttons: true
    }).then(function(value) {
        if(value === true) {
        $("#addComment").modal('hide');
        }else{
            $("#addComment").modal('show');  
        }
    });
    });
    $("#firstDateReturned").persianDatepicker({
        cellWidth: 30,
        cellHeight: 12,
        fontSize: 12,
        formatDate: "YYYY/0M/0D"
    });
    $("#secondDateReturned").persianDatepicker({
        cellWidth: 30,
        cellHeight: 12,
        fontSize: 12,
        formatDate: "YYYY/0M/0D",
        onSelect:()=>{
            let secondDate=$("#secondDateReturned").val();
            let firstDate=$("#firstDateReturned").val();
            
             $.ajax({
                method: 'get',
                url: baseUrl + "/searchReturnedByDate",
                data: {
                    _token: "{{ csrf_token() }}",
                    secondDate: secondDate,
                    firstDate:firstDate
                },
                async: true,
                success: function(msg) {
                    moment.locale('en');
                    $("#returnedCustomerList").empty();
                    msg.forEach((element,index)=>{
                        $("#returnedCustomerList").append(`
                        <tr onclick="returnedCustomerStuff(this)">
                            <td>`+(index+1)+`</td>
                            <td>`+element.Name+`</td>
                            <td>`+element.PCode+`</td>
                            <td>`+element.peopeladdress+`</td>
                            <td>`+element.PhoneStr+`</td>
                            <td>`+element.name+` `+element.lastName+`</td>
                            <td>`+moment(element.returnDate, 'YYYY-M-D HH:mm:ss').locale('fa').format('HH:mm:ss YYYY/M/D')+`</td>
                            <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+element.PSN+`_`+element.adminId+`"></td>
                        </tr> `);
                    });
                },
                error: function(data) {alert("bad");}
            });
        }
    });



    // function getCustomerLocation() {
    //     if( $("#customerLocation").is(':checked') ){
    //           let csn = $("#customerSn").val();

    //           $.ajax({
    //             method: 'get',
    //             url: baseUrl + "/newCustomer",
    //             data: {
    //                 _token: "{{ csrf_token() }}",
    //                 csn: csn,
    //             },
    //             async: true,
    //             success: function(msg) {
    //                 element.value = "";
    //                 element.value = msg[0].comment;
    //             },
    //             error: function(data) {}
    //         });
    //     }
    //     else{
    //         alert("Checkbox Is not checked");
    //     }

    // }
     


// $('.crmDataTable').DataTable({
//         "order": [],
//         "columnDefs": [ {
//         "targets"  : 0,
//         "orderable": false,
//         },
//         { className: "dt[-head|-body]-center", targets: "_all" },
//     ],
   
//     searchable: true,
//     visible: true,
//     paginate: true,
//     info: true,
   
//     "language": {
//         "emptyTable": "هیچ داده‌ای در جدول وجود ندارد",
//         "info": "نمایش _START_ تا _END_ از _TOTAL_ ردیف",
//         "infoEmpty": "نمایش 0 تا 0 از 0 ردیف",
//         "infoFiltered": "(فیلتر شده از _MAX_ ردیف)",
//         "infoThousands": ",",
//         "lengthMenu": "نمایش _MENU_ ردیف",
//         "processing": "در حال پردازش...",
//         "search": "جستجو:",
//         "zeroRecords": "رکوردی با این مشخصات پیدا نشد",
//         "paginate": {
//             "next": "بعدی",
//             "previous": "قبلی",
//             "first": "ابتدا",
//             "last": "انتها"
//         },
//         "aria": {
//             "sortAscending": ": فعال سازی نمایش به صورت صعودی",
//             "sortDescending": ": فعال سازی نمایش به صورت نزولی"
//         },
//         "autoFill": {
//             "cancel": "انصراف",
//             "fill": "پر کردن همه سلول ها با ساختار سیستم",
//             "fillHorizontal": "پر کردن سلول به صورت افقی",
//             "fillVertical": "پرکردن سلول به صورت عمودی"
//         },
//         "buttons": {
//             "collection": "مجموعه",
//             "colvis": "قابلیت نمایش ستون",
//             "colvisRestore": "بازنشانی قابلیت نمایش",
//             "copy": "کپی",
//             "copySuccess": {
//                 "1": "یک ردیف داخل حافظه کپی شد",
//                 "_": "%ds ردیف داخل حافظه کپی شد"
//             },
//             "copyTitle": "کپی در حافظه",
//             "excel": "اکسل",
//             "pageLength": {
//                 "-1": "نمایش همه ردیف‌ها",
//                 "_": "نمایش %d ردیف"
//             },
//             "print": "چاپ",
//             "copyKeys": "برای کپی داده جدول در حافظه سیستم کلید های ctrl یا ⌘ + C را فشار دهید",
//             "csv": "فایل CSV",
//             "pdf": "فایل PDF",
//             "renameState": "تغییر نام",
//             "updateState": "به روز رسانی"
//         },
//         "searchBuilder": {
//             "add": "افزودن شرط",
//             "button": {
//                 "0": "جستجو ساز",
//                 "_": "جستجوساز (%d)"
//             },
//             "clearAll": "خالی کردن همه",
//             "condition": "شرط",
//             "conditions": {
//                 "date": {
//                     "after": "بعد از",
//                     "before": "بعد از",
//                     "between": "میان",
//                     "empty": "خالی",
//                     "equals": "برابر",
//                     "not": "نباشد",
//                     "notBetween": "میان نباشد",
//                     "notEmpty": "خالی نباشد"
//                 },
//                 "number": {
//                     "between": "میان",
//                     "empty": "خالی",
//                     "equals": "برابر",
//                     "gt": "بزرگتر از",
//                     "gte": "برابر یا بزرگتر از",
//                     "lt": "کمتر از",
//                     "lte": "برابر یا کمتر از",
//                     "not": "نباشد",
//                     "notBetween": "میان نباشد",
//                     "notEmpty": "خالی نباشد"
//                 },
//                 "string": {
//                     "contains": "حاوی",
//                     "empty": "خالی",
//                     "endsWith": "به پایان می رسد با",
//                     "equals": "برابر",
//                     "not": "نباشد",
//                     "notEmpty": "خالی نباشد",
//                     "startsWith": "شروع  شود با",
//                     "notContains": "نباشد حاوی",
//                     "notEnds": "پایان نیابد با",
//                     "notStarts": "شروع نشود با"
//                 },
//                 "array": {
//                     "equals": "برابر",
//                     "empty": "خالی",
//                     "contains": "حاوی",
//                     "not": "نباشد",
//                     "notEmpty": "خالی نباشد",
//                     "without": "بدون"
//                 }
//             },
//             "data": "اطلاعات",
//             "logicAnd": "و",
//             "logicOr": "یا",
//             "title": {
//                 "0": "جستجو ساز",
//                 "_": "جستجوساز (%d)"
//             },
//             "value": "مقدار",
//             "deleteTitle": "حذف شرط فیلتر",
//             "leftTitle": "شرط بیرونی",
//             "rightTitle": "شرط داخلی"
//         },
//         "select": {
//             "cells": {
//                 "1": "1 سلول انتخاب شد",
//                 "_": "%d سلول انتخاب شد"
//             },
//             "columns": {
//                 "1": "یک ستون انتخاب شد",
//                 "_": "%d ستون انتخاب شد"
//             },
//             "rows": {
//                 "1": "1ردیف انتخاب شد",
//                 "_": "%d  انتخاب شد"
//             }
//         },
//         "thousands": ",",
//         "searchPanes": {
//             "clearMessage": "همه را پاک کن",
//             "collapse": {
//                 "0": "صفحه جستجو",
//                 "_": "صفحه جستجو (٪ d)"
//             },
//             "count": "{total}",
//             "countFiltered": "{shown} ({total})",
//             "emptyPanes": "صفحه جستجو وجود ندارد",
//             "loadMessage": "در حال بارگیری صفحات جستجو ...",
//             "title": "فیلترهای فعال - %d",
//             "showMessage": "نمایش همه"
//         },
//         "loadingRecords": "در حال بارگذاری...",
//         "datetime": {
//             "previous": "قبلی",
//             "next": "بعدی",
//             "hours": "ساعت",
//             "minutes": "دقیقه",
//             "seconds": "ثانیه",
//             "amPm": [
//                 "صبح",
//                 "عصر"
//             ],
//             "months": {
//                 "0": "ژانویه",
//                 "1": "فوریه",
//                 "10": "نوامبر",
//                 "2": "مارچ",
//                 "4": "می",
//                 "6": "جولای",
//                 "8": "سپتامبر",
//                 "11": "دسامبر",
//                 "3": "آوریل",
//                 "5": "جون",
//                 "7": "آست",
//                 "9": "اکتبر"
//             },
//             "unknown": "-",
//             "weekdays": [
//                 "یکشنبه",
//                 "دوشنبه",
//                 "سه‌شنبه",
//                 "چهارشنبه",
//                 "پنجشنبه",
//                 "جمعه",
//                 "شنبه"
//             ]
//         },
//         "editor": {
//             "close": "بستن",
//             "create": {
//                 "button": "جدید",
//                 "title": "ثبت جدید",
//                 "submit": "ایجــاد"
//             },
//             "edit": {
//                 "button": "ویرایش",
//                 "title": "ویرایش",
//                 "submit": "به‌روزرسانی"
//             },
//             "remove": {
//                 "button": "حذف",
//                 "title": "حذف",
//                 "submit": "حذف",
//                 "confirm": {
//                     "_": "آیا از حذف %d خط اطمینان دارید؟",
//                     "1": "آیا از حذف یک خط اطمینان دارید؟"
//                 }
//             },
//             "multi": {
//                 "restore": "واگرد",
//                 "noMulti": "این ورودی را می توان به صورت جداگانه ویرایش کرد، اما نه بخشی از یک گروه"
//             }
//         },
//         "decimal": ".",
//         "stateRestore": {
//             "creationModal": {
//                 "button": "ایجاد",
//                 "columns": {
//                     "search": "جستجوی ستون",
//                     "visible": "وضعیت نمایش ستون"
//                 },
//                 "name": "نام:",
//                 "order": "مرتب سازی",
//                 "paging": "صفحه بندی",
//                 "search": "جستجو",
//                 "select": "انتخاب",
//                 "title": "ایجاد وضعیت جدید",
//                 "toggleLabel": "شامل:"
//             },
//             "emptyError": "نام نمیتواند خالی باشد.",
//             "removeConfirm": "آیا از حذف %s مطمئنید؟",
//             "removeJoiner": "و",
//             "removeSubmit": "حذف",
//             "renameButton": "تغییر نام",
//             "renameLabel": "نام جدید برای $s :"
//         }
//     }
// });
// $('#allCustomers').DataTable({
//     "order": [],
//     "columnDefs": [ {
//     "targets"  : [ 0, 3 ],
//     "orderable": false,
//     },
//     { className: "dt[-head|-body]-center", targets: "_all" },
// ],
//     searching: false,
//     visible: false,
//     paginate: false,
//     info: false,}); 

    // $('#addedCustomers').DataTable({
    //     "order": [],
    //     "columnDefs": [ {
    //     "targets"  : [ 0, 3 ],
    //     "orderable": false,
    //     },
    //     { className: "dt[-head|-body]-center", targets: "_all" },
    // ],    
    //     searching: false,
    //     visible: false,
    //     paginate: false,
    //     info: false,});


    // $('.karbarTable').DataTable({
    //     "order": [], "columnDefs": [ {
    //     "targets"  : [ 0, 4 ],
    //     "orderable": false,
    //     },
    //     { className: "dt[-head|-body]-center", targets: "_all" },
    // ],    
    //     searching: false, visible: false, paginate: false,
    //     info: false});


