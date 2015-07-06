// Capacity editor generator
// 
// Requires jQuery

// Global variables for init
var CAUworkflow = {};

var wf = window.CAUworkflow;
wf.memberCount = 1;
var count = wf.memberCount;

// Capacity class
function capacity() {
    
    // Annual Days
    /*
    this.annual = {};
    this.annual.year = 365;
    this.annual.weekend = 104;
    this.annual.publicHoliday = 13;
    this.annual.rdo = 12;
    this.annual.leave = 20;
    this.annual.sick = 10;
    */
    
    // Hours
    this.hours = {};
    this.hours.start = "8:30";
    this.hours.end = "17:00";
    this.hours.work = 7.25;
    
    this.team = {};
    this.team.members = {};
    this.team.name = {};
    //this.team.members.hours = {};
    //this.team.members.days = [];
    
    // Recurring meetings
    this.days = {};
    this.days.meetings = {};
    this.days.meetings.mon = 0;
    this.days.meetings.tues = 1;
    this.days.meetings.weds = 1.5;
    this.days.meetings.thurs = 0;
    this.days.meetings.fri = 0;
    
    this.days.productive = {};
    
    // Variables
    this.var = {};
    this.var.productivity = 0.83;
    
    this.f_CalcProductivity = function(prod) {
        this.hours.productive = this.hours.work * prod;
    }
    
    // Productive hours
    this.f_CalcProductiveHours = function() {
        this.days.productive.mon = this.hours.productive - this.days.meetings.mon;
        this.days.productive.tues = this.hours.productive - this.days.meetings.tues;
        this.days.productive.weds = this.hours.productive - this.days.meetings.weds;
        this.days.productive.thurs = this.hours.productive - this.days.meetings.thurs;
        this.days.productive.fri = this.hours.productive - this.days.meetings.fri;
    }
    
    this.f_CalcWorkDays = function() {
        var year = this.annual.year,
            weekend = this.annual.weekend, 
            pholiday = this.annual.publicHoliday = 13,
            rdo = this.annual.rdo = 12,
            leave = this.annual.leave = 20,
            sick = this.annual.sick = 10;
    
        this.annual.workdays = year - weekend - pholiday - rdo - sick - leave; 
    }
    
    this.f_CalcIndividualCapacity = function(days) {
        var workHours = 0;
        
        if(typeof days == 'undefined') {
            return false;
        }
        
        if($.inArray("mon", days) >= 0) {
            workHours = workHours + this.days.productive.mon;
            //console.log("Mon " + workHours);
        }
        if($.inArray("tues", days) >= 0) {
            workHours = workHours + this.days.productive.tues;
            //console.log("Tues " + workHours);
        }
        if($.inArray("weds", days) >= 0) {
            workHours = workHours + this.days.productive.weds;
            //console.log("Wed " + workHours);
        }
        if($.inArray("thurs", days) >= 0) {
            workHours = workHours + this.days.productive.thurs;
            //console.log("Thur " + workHours);
        }
        if($.inArray("fri", days) >= 0) {
            workHours = workHours + this.days.productive.fri;
            //console.log("Fri " + workHours);
        }
        
        this.hours.workWeek = workHours;
        var perday = workHours/ (days.length + 1);
        
        this.hours.perday = perday;
        
        return workHours;
    }
    
    this.f_CalcTeamCapacity = function(group) {
        var teamHours = 0,        
            cap = this;
        
        $.each( group, function(name, days) {
            //console.log(name);
            //console.log(days);
            
            var indiv = cap.f_CalcIndividualCapacity(days);
            //console.log("INDIV " + indiv);
            cap.team.members[name] = {};
            cap.team.members[name]['hours'] = indiv;
            cap.team.members[name]['days'] = days;
            teamHours = teamHours + indiv;
        });
        
        cap.team.total = teamHours;
        cap.team.day = teamHours / 5;
        return teamHours;
    }
}

// Test stuff
var days = ["mon", "tues", "weds", "thurs", "fri"];
var josh = new capacity();
josh.f_CalcProductivity(0.83);
josh.f_CalcProductiveHours();
//josh.f_CalcIndividualCapacity(days);

var team = {
    'josh' : ["mon", "tues", "weds", "thurs", "fri"],
    'jon' : ["mon", "tues", "weds", "thurs", "fri"],
    'tristan' : ["tues", "weds", "thurs"],
    'mon' : ["tues", "thurs"]
};

var coms = new capacity();
coms.f_CalcProductivity(0.83);
coms.f_CalcProductiveHours();
coms.f_CalcTeamCapacity(team);

var evil_coms = new capacity();
evil_coms.f_CalcProductivity(0.83);
evil_coms.f_CalcProductiveHours();


// JQuery form building
function createTeam(appendTo) {
    var container = $(appendTo);
    
    var team = '<br/><div class="team"><div class="row"><label class="col-xs-1" for="name">Team</label><div class="col-xs-5"><input type="text" class="form-control" id="exampleInputEmail1" placeholder="Team name"></div><div class="col-xs-6"></div></div><div class="row"><br /><div class="col-xs-offset-1 col-xs-11"><table class="table table-striped center-td"><tr><th>Name</th><th>Mon</th><th>Tues</th><th>Weds</th><th>Thurs</th><th>Fri</th><th><button class="btn btn-primary btn-xs pull-right"><span class="glyphicon glyphicon-plus"></span> member</button></th></tr><tr><td><input type="text" class="form-control" id="exampleInputEmail1" placeholder="Johnny Jon"></td><td><input type="checkbox" data-id="dayCheckBoxMon" name="dayCheckBoxMon" value="mon"></td><td><input type="checkbox" data-id="dayCheckBoxTues" name="dayCheckBoxTues" value="tues"></td><td><input type="checkbox" data-id="dayCheckBoxWeds" name="dayCheckBoxWeds" value="weds"></td><td><input type="checkbox" data-id="dayCheckBoxThurs" name="dayCheckBoxThurs" value="thurs"></td><td><input type="checkbox" data-id="dayCheckBoxFri" name="dayCheckBoxFri" value="fri"></td><td></td></tr></table></div></div></div>';
    
    container.append(team);
}

function createMember(appendTo, wf) {
    var container = $(appendTo),
        n = wf.memberCount;
    
    var team = '<tr><td><input type="text" class="form-control" id="nameInpt" name="member-'+n+'_name" placeholder="New member"></td><td><input type="checkbox" data-flop="dayCheckBoxMon" name="member-'+n+'_days" value="mon" checked></td><td><input type="checkbox" data-flop="dayCheckBoxTues" name="member-'+n+'_days" value="tues" checked></td><td><input type="checkbox" data-flop="dayCheckBoxWeds" name="member-'+n+'_days" value="weds" checked></td><td><input type="checkbox" data-flop="dayCheckBoxThurs" name="member-'+n+'_days" value="thurs" checked></td><td><input type="checkbox" data-flop="dayCheckBoxFri" name="member-'+n+'_days" value="fri" checked></td><td><input type="hidden" name="member-'+n+'_days" value="sat" checked></td></tr>';
    
    container.append(team);
    wf.memberCount++;
    return wf.memberCount;
}

function buildTeamObj(form) {
    var team = form.team_name;
    var count = form.team_count;
    var obj = {};
    obj[team] = {};
    
    for (var i = 0; i < count; i++ ) {
        var memberName = "member-"+i+"_name";
        var memberDays = "member-"+i+"_days";
        var name = form[memberName];
        var days = form[memberDays];

        obj[team][name] = days;
    }
    
    return obj;
}

function addDeleteMemberButtonEvents() {
    var btns = $(".btn-remove-member");
    
    // Unbind previous events
    btns.off();
    
    // Bind new delete events.
    btns.click(function(event) {
        event.preventDefault();
        $(this).parent().parent().remove();
        wf.memberCount = $("#team1CapacityForm tbody tr").length;
    });
}


// Jquery event listeners
$("#addTeamBtn").click(function(event) {
    event.preventDefault();
    createTeam("#capacityForm");
});
$("#finishCapacityBtn").click(function(event) {
    event.preventDefault();
    $("#team1CapacityForm").submit();
});

$(".add-member-btn").click(function(event) {
    event.preventDefault();
    createMember("#members_append", wf);
    
    var n = $("#team1CapacityForm tbody tr").length;
    $("#team1_count").val(n);
    
});

$("#teamInpt").change(function(event) {
    event.preventDefault();
    
    var tbody = $("#members_append"),
        team = $(this).val();
    
    tbody.empty();
    console.log(team);
    
    $.ajax({
        type: 'GET',
        url: 'capacity.php',
        data: 'team=' +team,
        success: function(msg) {
            tbody.html(msg);
            wf.memberCount = $("#team1CapacityForm tbody tr").length;
            addDeleteMemberButtonEvents();
        },
        error: function(msg) {
            console.log(msg);
            tbody.html(msg.responseText);
        }
    });
    
    
});



//createTeam("#capacityForm");


// Serialize the form.
$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

$(function() {
    $('#team1CapacityForm').submit(function() {
        //$('#result').text(JSON.stringify($('#team1CapacityForm').serializeObject()));
        var output = $('#team1CapacityForm').serializeObject();
        var teams = buildTeamObj(output);
        var capObj = new capacity();
        //console.log(output);
        capObj.f_CalcProductivity(0.83);
        capObj.f_CalcProductiveHours();
        
        $.each( teams, function( team, days ) {
            //console.log(team);
            //console.log(days);
            capObj.team.name = team;
            
            capObj.f_CalcTeamCapacity(days);
        });
        
        var param = $.param(capObj.team);
        
        $.ajax({
            type: 'POST',
            url: 'capacity.php',
            data: param,
            success: function(msg) {
                $("#result").html(msg);
            },
            error: function(msg) {
                console.log(msg);
                $("#result").html(msg.responseText);
            }
        });
        
        return false;
    });
});