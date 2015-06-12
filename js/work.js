// Work class generator
// 
// Requires - jQuery, moment.js, chart.js
// Experimental - Intergration with Highcharts (it is better than chart.js)

function work() {
    
    // Get range and set range start and end 
    this.getRange = function() {
        var start = $("#sDate").val();
        var s = moment(start,"YYYY-MM-DD");
            
        var end = $("#eDate").val();
        var e = moment(end,"YYYY-MM-DD");
        var n = 0;
        var d = 0;
            // Work around for date weirdness
            
            /*
            z = new Date(s.setDate(
                s.getDate() - 1
            )),
            x = new Date(s.setDate(
                s.getDate() - 1
            )), 
            */
        
        this.range = {};
        this.range.start = start;
        this.range.end = end;
        this.range.chart = [];
        
        var tl = this.range.chart;
       
        while(s <= e) {
            // if weekend don't count
            if ( s.day() == 0 || s.day() == 6 ) {
            } else {
               tl.push(s.format("YYYY-MM-DD"));
               n++;  
            
            }
            
            
            //console.log(s.format("YYYY-MM-DD"));
            
            s.add(1, 'days');
            /*
            z = new Date(z.setDate(
                z.getDate() + 1
            ));
            */
            
            d++;
        }
        
        this.range.workdays = n;
        this.range.duration = d;
    }
    
    // Creates the capcity timeline and totals properties, may need to modify it to take an object as an input with the capacity - that way can print front PHP MYSQL. Also this object could set the names of all departments and make adding extras very easy. Think scalable.
    
    this.getCapacity = function(cap) {   
        // These numbers need to be pulled dynamically,
        // This is capacity per day
        /*
        var prod = 11.29032258,
            coms = 17.83870968,
            dig = 12.19354839,
            des = 6.774193548,
            vid = 7.451612903,
            ext = 1;
        */
        var prod = Number(cap.cap_product),
            coms = Number(cap.cap_coms),
            dig =  Number(cap.cap_digital),
            des =  Number(cap.cap_design),
            vid =  Number(cap.cap_video),
            ext =  Number(cap.cap_external);
        
        var start = moment(this.range.start,"YYYY-MM-DD"),
            s = start,
            e = moment(this.range.end,"YYYY-MM-DD");
        
        this.capacity = {};
        
        // create empty array for date range
        this.capacity.tl = [];
        
        // create array of dates with capacity
        while(s.format("X") <= e.format("X")) {
            // if weekend set capacity at 0
            if ( s.day() == 0 || s.day() == 6 ) {
                var point = {
                    "date" : new Date(s.format("YYYY-MM-DD")),
                    "label" : s.format("YYYY-MM-DD"),
                    "product" : 0,
                    "coms" : 0,
                    "digital" : 0,
                    "design" : 0,
                    "video" : 0 /*,
                    "external" : 0 */
                } 
            } else {
                var point = {
                    "date" : new Date(s.format("YYYY-MM-DD")),
                    "label" : s.format("YYYY-MM-DD"),
                    "product" : prod,
                    "coms" : coms,
                    "digital" : dig,
                    "design" : des,
                    "video" : vid /*,
                    "external" : ext */
                }  
            }
            
            this.capacity.tl.push(point);
            /*
            s = new Date(s.setDate(
                s.getDate() + 1
            )); */
            //console.log(s + "**");
            s.add(1, 'days');
        }
        
        // Create and set total capacity at 0 so we can add on top of it
        this.capacity.total = {
            "product" : 0,
            "coms" : 0,
            "digital" : 0,
            "design" : 0,
            "video" : 0 /*,
            "external" : 0 */
        }
        
        // Loop through timeline and create capcity totals
        for (i = 0; i < this.capacity.tl.length; i++) {
            var point = this.capacity.tl[i],
                total = this.capacity.total;
            
            $.each( point, function( key, value ) {
                if (typeof value === "number") {
                    total[key] += point[key]; 
                }
            });

        }
        
        // Create and store keys
        
        var cap = this.capacity.total;
        
        this.range.depts = [];
        var keys = this.range.depts;
        
        $.each( cap, function ( key, value ) {
            if ( typeof value === "number" ) {
                keys.push(key);
            }
        });
        
    }
    
    this.getWorkload = function(tasks) {
        this.load = {};
        
        // create empty array for date range
        this.load.tl = [];
        
        var start = moment(this.range.start,"YYYY-MM-DD"),
            s = start,
            e = moment(this.range.end,"YYYY-MM-DD");
        
        // Build range
        while(s.format("X") <= e.format("X")) {
            // create date point template
            var point = {};

            // grab all prams of tasks and set value to 0 for point prototype - every date it resets to zero
            $.each( tasks[0], function( key, value ) {
                if (typeof value === "number") {
                    point[key] = 0; 
                }
            });
            
            
            // if weekend set load at 0
            if ( s.day() == 0 || s.day() == 6 ) {
                point.date = new Date(s.format("YYYY-MM-DD")); //new Date(s - 1);
                point.label = s.format("YYYY-MM-DD");

            // find if task falls on date and add all values
            } else {
                
                var date = new Date(s.format("YYYY-MM-DD")); //new Date(s - 1);
                
                point.date = new Date(s.format("YYYY-MM-DD")); //new Date(s - 1);
                point.label = s.format("YYYY-MM-DD");
                
                // Loop through task list
                for ( var i = 0; i < tasks.length; i++ ) {
                    var tS = new Date(tasks[i].sdate),
                        tE = new Date(tasks[i].edate);
                    
                    // If current date has a task on it
                    if (tS <= date && date <= tE) {
                        //console.log(tasks[i].task);
                        // Add values up
                        var t = tasks[i];
                        
                        $.each( t, function( key, value ) {
                            if (typeof value === "number") {
                               point[key] += value;  
                            } 
                        });
                    } else {
                    }
                }   
            }
            //console.log(point);
            this.load.tl.push(point);
            
            /*
            s = new Date(s.setDate(
                s.getDate() + 1
            )); */
            s.add(1, 'days');
        }
        
        // Create grand total load set at 0 to iterate on
        this.load.total = {};
        
        var total = this.load.total;
        
        $.each( tasks[0], function( key, value ) {
           total[key] = 0; 
        });
        
        // Loop and add total loads
        for (i = 0; i < this.load.tl.length; i++) {
            var point = this.load.tl[i];
            
            $.each( point, function( key, value ) {
                if (typeof value === "number") {
                    total[key] += value; 
                }
            });

        }
    }
    
    
    this.getComparison = function() {
        this.avail = {};
        this.avail.total = {};
        this.avail.tl = [];
        this.avail.data = {};
        this.range.data = [];
        this.avail.chart = {};
        
        var avail = this.avail.total,
            tl = this.avail.tl;
        
        // Compare total capacity and workload
        var load = this.load.total,
            capacity = this.capacity.total;
        
        $.each( capacity, function( key, value ) {
            if (typeof value === "number") {
                avail[key] = (100 / capacity[key]) * load[key]; 
            }
        });

        
        // Compare each day
        
        var len = this.capacity.tl.length,
            loadtl = this.load.tl,
            captl = this.capacity.tl,
            s = moment(this.range.start,"YYYY-MM-DD"),
            e = moment(this.range.end,"YYYY-MM-DD"),
            range = this.range.data,
            data = this.avail.data,
            chart = this.avail.chart;
        
        // Set up availibilty data ranges as arrays (for charting)
        $.each( captl[0], function( key, value ) {
            if (typeof value === "number" ) {
                data[key] = [];
                chart[key] = [];
            }
        });
        
        // Loop through and build arrays of data for each dept
        for(var x = 0; x < len; x++ ) {
            var point = {};
            
            if(captl[x].label === loadtl[x].label) {
                point.label = captl[x].label;
                point.date = captl[x].date;
                
                //console.log(point.label);
                
                $.each( captl[x], function( key, value ) {
                    
                    if (typeof value === "number" && value !== 0 ) {
                        var pc =  (100 / captl[x][key]) * loadtl[x][key];
                        
                        var pcr = Math.round(pc);
                        
                        point[key] = pc;
                        data[key].push(pc);
                        
                        var day = moment(point.label,"YYYY-MM-DD").day();
                        
                        if ( day > 0 && day < 6 ) {
                            chart[key].push(pcr);
                        }
                        
                    } else if ( typeof value === "number" && value === 0 ) {
                        point[key] = 0; 
                        data[key].push(0);
                    }
                });
                
                // Push date object into array
                range.push(point.label);
                tl.push(point);
            }
        }
    }
    
    // Build chart using data created by running avail.
    this.buildChart = function(chartID, dept, dept2, dept3) {
        var avail = this.avail.data,
            range = this.range.data;
        
        var data = {
            labels: range,
            datasets: [
                    {
                        label: dept,
                        fillColor: "rgba(220,220,220,0.2)",
                        strokeColor: "rgba(220,220,220,1)",
                        pointColor: "rgba(220,220,220,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data: avail[dept]
                    },
                    {
                        label: dept2,
                        fillColor: "rgba(0,123,123,0.2)",
                        strokeColor: "rgba(0,123,123,1)",
                        pointColor: "rgba(0,123,123,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(0,123,123,1)",
                        data: avail[dept2]
                    },
                    {
                        label: dept3,
                        fillColor: "rgba(180,40,40,0.2)",
                        strokeColor: "rgba(180,40,40,1)",
                        pointColor: "rgba(180,40,40,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(180,40,40,1)",
                        data: avail[dept3]
                    }
                ]
            };
        
        // Create chart
        var chart = document.getElementById(chartID).getContext("2d");
        var ctx = new Chart(chart).Line(data);
        
        this.chart = ctx;
    }
    
    // Build progress bars using data from avail
    this.buildLines = function() {
        var avail = this.avail.total;
        
        $.each( avail, function( key, value ) {
            if (typeof value === "number") {
                var bar = $("#bar-" + key);

                bar.attr("style", "width: " + value/2 + "%").text( Math.round(value) + "%");

                if ( value < 79) {
                    bar.removeClass("progress-bar-warning progress-bar-danger progress-bar-striped").addClass("progress-bar-success");
                } else if( value > 79 && value < 100 ) {
                    bar.removeClass("progress-bar-success progress-bar-danger progress-bar-striped").addClass("progress-bar-warning");
                } else if ( value > 100 && value < 150 ) {
                    bar.removeClass("progress-bar-success progress-bar-warning progress-bar-striped").addClass("progress-bar-danger");
                } else if ( value >= 150 ) {
                    bar.removeClass("progress-bar-success progress-bar-warning").addClass("progress-bar-danger progress-bar-striped");
                }

            } 
        });
    }
    
    // Generate proposal amounts
    this.getProposal = function() {
        var depts = this.range.depts,
            time = this.range.workdays;
        
        this.proposal = {};
        this.proposal.raw = {};
        this.proposal.load = {};
        
        for( var i = 0; i < depts.length; i++ ) {
            var dept = depts[i],
                raw = Number($("[name*=" + dept + "]").text()),
                cap = this.capacity.total[dept];
            console.log(raw);
            this.proposal.raw[dept] = raw;
            this.proposal.load[dept] = (100 / cap) * raw; 
        }
    }
    
    this.buildPropLines = function() {
        var prop = this.proposal.load;
        
        $.each( prop, function( key, value ) {
            if (typeof value === "number") {
                var bar = $("#bar-" + key + "-prop");

                bar.attr("style", "width: " + value/2 + "%").text( Math.round(value) + "%");
            } 
        });
    }
    
    // Generate High Chart
    this.buildHighChart = function(chartID) {
        var avail = this.avail.chart,
            range = this.range.chart,
            depts = this.range.depts,
            series = [];
        
        for ( var i = 0; i < depts.length; i++ ) {
            var obj = {};
                obj.name = depts[i];
                obj.data = avail[depts[i]];
                //obj.negativeColor = '#0088FF';
                obj.threshold = 100;
            
                series.push(obj);            
        }
        
        var options = {
                chart: { 
                    renderTo: chartID 
                },
                title: {
                    text: 'Workload',
                    x: -20 //center
                },
                subtitle: {
                    text: 'Anything over 100% is over capacity',
                    x: -20
                },
                xAxis: {
                    categories: range
                },
                yAxis: {
                    title: {
                        text: 'Capacity (%)'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }],
                    min: 0,
                    max: 300
                },
                tooltip: {
                    valueSuffix: '%'
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: series
            }
        
        var highChart = new Highcharts.Chart(options);
        
        this.highChart = highChart;
           
    }
    
    this.buildHighStock = function(chartID) {
        // moment(wl.range.start).format("x")
        // moment(this.range.end,"YYYY-MM-DD")
        // part of series is [ date(x), number ]
        /* [

[1147651200000,376.20],
[1147737600000,371.30],
[1147824000000,374.50],
[1147910400000,370.99],
[1147996800000,370.02],
[1148256000000,370.95],
[1148342400000,375.58],
[1148428800000,381.25],
[1148515200000,382.99],
[1148601600000,381.35],
[1148947200000,371.94],
[1149033600000,371.82], 
    
    */
        var avail = this.avail.chart,
            range = this.range.chart,
            depts = this.range.depts,
            series = [];
        
        for ( var i = 0; i < depts.length; i++ ) {
            var obj = {};
                obj.name = depts[i];
                obj.data = [];
                for ( var j = 0; j < range.length ; j++) {
                    var arr = [
                        Number(moment(range[j], "YYYY-MM-DD").format("x")),
                        avail[depts[i]][j]
                        ];
                    
                    obj.data.push(arr);
                }
                
                series.push(obj);            
        }
        
        this.test = series;
        
        var options = {
                chart: { 
                    renderTo: chartID 
                },
                title: {
                    text: 'Workload',
                    x: -20 //center
                },
                subtitle: {
                    text: 'Anything over 100% is over capacity',
                    x: -20
                },
                series: series
            }
        
        var stockChart = new Highcharts.StockChart(options);
        
        this.stockChart = stockChart;
        
    }
    

    // THIS BUILDS ALL
    this.build = function(tasks, capacity) {
        this.getRange();
        this.getCapacity(capacity);
        this.getWorkload(tasks);
        this.getComparison();
        this.buildLines();
        
        // Kill previous chart before making a new one
        if(this.hasOwnProperty('highChart')){
            this.highChart.destroy();
        }
        // Build new HighChart
        this.buildHighChart("hc");
    }
    
    this.buildAllCharts = function() {    
        
        var cap = this.capacity.total,
            keys = [];
        
        $.each( cap, function ( key, value ) {
            if ( typeof value === "number" ) {
                keys.push(key);
            }
        }); 
        
        for( var i = 0; i < keys.length; i++ ) {
            var id = keys[i] + "Chart";
            this.buildChart(id, keys[i]);
        }
    } 
}


var taskList = [
  {
    "task":"super",
    "product":10,
    "coms":10,
    "digital":10,
    "design":10,
    "video":10,
    "external":0,
    "sdate":"2015-11-04",
    "edate":"2015-11-12",
    "duration":8,
    "workdays":7
  },
  {
    "task":"tour 7",
    "product":2,
    "coms":2,
    "digital":6,
    "design":4.5,
    "video":0,
    "external":0,
    "sdate":"2015-10-05",
    "edate":"2015-10-06",
    "duration":1,
    "workdays":2
  },
  {
    "task":"tour 10",
    "product":3.727272727,
    "coms":3.727272727,
    "digital":11.13636364,
    "design":5.681818182,
    "video":2.454545455,
    "external":0,
    "sdate":"2015-10-01",
    "edate":"2015-10-30",
    "duration":29,
    "workdays":22
  },
  {
    "task":"kingdra",
    "product":0.777777778,
    "coms":0.777777778,
    "digital":0,
    "design":0.222222222,
    "video":0.111111111,
    "external":0.777777778,
    "sdate":"2015-10-01",
    "edate":"2015-10-13",
    "duration":12,
    "workdays":9
  },
  {
    "task":"tour 2",
    "product":1.5,
    "coms":1.5,
    "digital":1,
    "design":7,
    "video":0,
    "external":0,
    "sdate":"2015-10-01",
    "edate":"2015-10-02",
    "duration":1,
    "workdays":2
  },
  {
    "task":"water",
    "product":0.4375,
    "coms":0.4375,
    "digital":0.0625,
    "design":0.5,
    "video":0,
    "external":0,
    "sdate":"2015-09-14",
    "edate":"2015-10-05",
    "duration":21,
    "workdays":16
  },
  {
    "task":"wallet",
    "product":0.125,
    "coms":0.125,
    "digital":0.05,
    "design":0.175,
    "video":0,
    "external":0,
    "sdate":"2015-09-05",
    "edate":"2015-10-30",
    "duration":55,
    "workdays":40
  },
  {
    "task":"raichu",
    "product":0.833333333,
    "coms":0.833333333,
    "digital":0,
    "design":0,
    "video":0,
    "external":0,
    "sdate":"2015-09-05",
    "edate":"2015-09-14",
    "duration":9,
    "workdays":6
  },
  {
    "task":"mon",
    "product":1.75,
    "coms":1.75,
    "digital":0.5,
    "design":1.25,
    "video":0,
    "external":0,
    "sdate":"2015-09-05",
    "edate":"2015-09-10",
    "duration":5,
    "workdays":4
  },
  {
    "task":"kfc",
    "product":0.159090909,
    "coms":0.159090909,
    "digital":1.272727273,
    "design":0.318181818,
    "video":0.318181818,
    "external":0,
    "sdate":"2015-09-01",
    "edate":"2015-10-30",
    "duration":59,
    "workdays":44
  },
  {
    "task":"joseph",
    "product":2,
    "coms":2,
    "digital":0.52173913,
    "design":0,
    "video":0.608695652,
    "external":0.608695652,
    "sdate":"2015-09-01",
    "edate":"2015-10-01",
    "duration":30,
    "workdays":23
  },
  {
    "task":"bourbon",
    "product":0.083333333,
    "coms":0.083333333,
    "digital":0,
    "design":0.8,
    "video":0,
    "external":0,
    "sdate":"2015-08-09",
    "edate":"2015-10-30",
    "duration":82,
    "workdays":60
  },
  {
    "task":"neat",
    "product":4.102564103,
    "coms":4.102564103,
    "digital":0.179487179,
    "design":0.512820513,
    "video":1.538461538,
    "external":0,
    "sdate":"2015-08-09",
    "edate":"2015-10-01",
    "duration":53,
    "workdays":39
  },
  {
    "task":"fast food",
    "product":0.318181818,
    "coms":0.318181818,
    "digital":0.090909091,
    "design":0.227272727,
    "video":0,
    "external":0,
    "sdate":"2015-08-09",
    "edate":"2015-09-08",
    "duration":30,
    "workdays":22
  },
  {
    "task":"tour 4",
    "product":0.5,
    "coms":0.5,
    "digital":0,
    "design":0,
    "video":0,
    "external":0,
    "sdate":"2015-08-09",
    "edate":"2015-08-23",
    "duration":14,
    "workdays":10
  },
  {
    "task":"jon",
    "product":0.318181818,
    "coms":0.318181818,
    "digital":0,
    "design":0.090909091,
    "video":0.045454545,
    "external":0.318181818,
    "sdate":"2015-08-01",
    "edate":"2015-09-01",
    "duration":31,
    "workdays":22
  },
  {
    "task":"zekrom",
    "product":0.111111111,
    "coms":0.111111111,
    "digital":0.079365079,
    "design":0.063492063,
    "video":0,
    "external":0,
    "sdate":"2015-07-08",
    "edate":"2015-10-02",
    "duration":86,
    "workdays":63
  },
  {
    "task":"starmie",
    "product":3.565217391,
    "coms":3.565217391,
    "digital":10.65217391,
    "design":5.434782609,
    "video":2.347826087,
    "external":0,
    "sdate":"2015-07-08",
    "edate":"2015-08-09",
    "duration":32,
    "workdays":23
  },
  {
    "task":"zinger",
    "product":2.333333333,
    "coms":2.333333333,
    "digital":0.333333333,
    "design":2.666666667,
    "video":0,
    "external":0,
    "sdate":"2015-07-08",
    "edate":"2015-07-12",
    "duration":4,
    "workdays":3
  },
  {
    "task":"john",
    "product":0.173913043,
    "coms":0.173913043,
    "digital":0.52173913,
    "design":0.391304348,
    "video":0,
    "external":0,
    "sdate":"2015-07-01",
    "edate":"2015-08-01",
    "duration":31,
    "workdays":23
  },
  {
    "task":"tour 6",
    "product":0.116666667,
    "coms":0.116666667,
    "digital":0.083333333,
    "design":0.066666667,
    "video":0,
    "external":0,
    "sdate":"2015-06-23",
    "edate":"2015-09-14",
    "duration":83,
    "workdays":60
  },
  {
    "task":"cmpsn",
    "product":2.333333333,
    "coms":2.333333333,
    "digital":4.666666667,
    "design":1.666666667,
    "video":4.666666667,
    "external":0,
    "sdate":"2015-06-23",
    "edate":"2015-06-25",
    "duration":2,
    "workdays":3
  },
  {
    "task":"turtwig",
    "product":0.03,
    "coms":0.03,
    "digital":0.02,
    "design":0.14,
    "video":0,
    "external":0,
    "sdate":"2015-06-13",
    "edate":"2015-10-30",
    "duration":139,
    "workdays":100
  },
  {
    "task":"pichu",
    "product":0.777777778,
    "coms":0.777777778,
    "digital":0,
    "design":2.555555556,
    "video":0,
    "external":0,
    "sdate":"2015-06-13",
    "edate":"2015-06-25",
    "duration":12,
    "workdays":9
  },
  {
    "task":"microphone",
    "product":3.5,
    "coms":3.5,
    "digital":28,
    "design":7,
    "video":7,
    "external":0,
    "sdate":"2015-06-13",
    "edate":"2015-06-16",
    "duration":3,
    "workdays":2
  },
  {
    "task":"llb",
    "product":0.058139535,
    "coms":0.058139535,
    "digital":0.023255814,
    "design":0.081395349,
    "video":0,
    "external":0,
    "sdate":"2015-06-09",
    "edate":"2015-10-06",
    "duration":119,
    "workdays":86
  },
  {
    "task":"tour 8",
    "product":3.5,
    "coms":3.5,
    "digital":0,
    "design":1,
    "video":0.5,
    "external":3.5,
    "sdate":"2015-06-09",
    "edate":"2015-06-10",
    "duration":1,
    "workdays":2
  },
  {
    "task":"tour 1",
    "product":0.054347826,
    "coms":0.054347826,
    "digital":0.02173913,
    "design":0.076086957,
    "video":0,
    "external":0,
    "sdate":"2015-06-08",
    "edate":"2015-10-13",
    "duration":127,
    "workdays":92
  },
  {
    "task":"voltorb",
    "product":1.333333333,
    "coms":1.333333333,
    "digital":4,
    "design":3,
    "video":0,
    "external":0,
    "sdate":"2015-06-08",
    "edate":"2015-06-10",
    "duration":2,
    "workdays":3
  },
  {
    "task":"pickachu",
    "product":4.324324324,
    "coms":4.324324324,
    "digital":0.189189189,
    "design":0.540540541,
    "video":1.621621622,
    "external":0,
    "sdate":"2015-06-03",
    "edate":"2015-07-23",
    "duration":50,
    "workdays":37
  },
  {
    "task":"dragonite",
    "product":0.511111111,
    "coms":0.511111111,
    "digital":0.133333333,
    "design":0,
    "video":0.155555556,
    "external":0.155555556,
    "sdate":"2015-06-01",
    "edate":"2015-10-02",
    "duration":123,
    "workdays":90
  },
  {
    "task":"singing",
    "product":0.097222222,
    "coms":0.097222222,
    "digital":1.180555556,
    "design":0.861111111,
    "video":0,
    "external":0,
    "sdate":"2015-06-01",
    "edate":"2015-09-08",
    "duration":99,
    "workdays":72
  },
  {
    "task":"tour 3",
    "product":5.714285714,
    "coms":5.714285714,
    "digital":0.25,
    "design":0.714285714,
    "video":2.142857143,
    "external":0,
    "sdate":"2015-06-01",
    "edate":"2015-07-08",
    "duration":37,
    "workdays":28
  },
  {
    "task":"dan",
    "product":0.304347826,
    "coms":0.304347826,
    "digital":0.217391304,
    "design":0.173913043,
    "video":0,
    "external":0,
    "sdate":"2015-06-01",
    "edate":"2015-07-01",
    "duration":30,
    "workdays":23
  },
  {
    "task":"lickitung",
    "product":0.116666667,
    "coms":0.116666667,
    "digital":0.033333333,
    "design":0.083333333,
    "video":0,
    "external":0,
    "sdate":"2015-05-31",
    "edate":"2015-08-23",
    "duration":84,
    "workdays":60
  },
  {
    "task":"tour 5",
    "product":0.368421053,
    "coms":0.368421053,
    "digital":0,
    "design":0,
    "video":0,
    "external":0,
    "sdate":"2015-05-31",
    "edate":"2015-06-25",
    "duration":25,
    "workdays":19
  },
  {
    "task":"lime",
    "product":2.333333333,
    "coms":2.333333333,
    "digital":0.666666667,
    "design":1.666666667,
    "video":0,
    "external":0,
    "sdate":"2015-05-27",
    "edate":"2015-05-29",
    "duration":2,
    "workdays":3
  },
  {
    "task":"orange",
    "product":1,
    "coms":1,
    "digital":3,
    "design":2.25,
    "video":0,
    "external":0,
    "sdate":"2015-05-20",
    "edate":"2015-05-25",
    "duration":5,
    "workdays":4
  },
  {
    "task":"mango",
    "product":2.333333333,
    "coms":2.333333333,
    "digital":18.66666667,
    "design":4.666666667,
    "video":4.666666667,
    "external":0,
    "sdate":"2015-05-20",
    "edate":"2015-05-24",
    "duration":4,
    "workdays":3
  },
  {
    "task":"tour 9",
    "product":0.666666667,
    "coms":0.666666667,
    "digital":0.173913043,
    "design":0,
    "video":0.202898551,
    "external":0.202898551,
    "sdate":"2015-05-05",
    "edate":"2015-08-09",
    "duration":96,
    "workdays":69
  },
  {
    "task":"bitters",
    "product":0.111111111,
    "coms":0.111111111,
    "digital":0.074074074,
    "design":0.518518519,
    "video":0,
    "external":0,
    "sdate":"2015-05-05",
    "edate":"2015-06-10",
    "duration":36,
    "workdays":27
  },
  {
    "task":"lemon",
    "product":0.636363636,
    "coms":0.636363636,
    "digital":0.454545455,
    "design":0.363636364,
    "video":0,
    "external":0,
    "sdate":"2015-05-04",
    "edate":"2015-05-18",
    "duration":14,
    "workdays":11
  },
  {
    "task":"zuccini",
    "product":3.538461538,
    "coms":3.538461538,
    "digital":0.923076923,
    "design":0,
    "video":1.076923077,
    "external":1.076923077,
    "sdate":"2015-05-02",
    "edate":"2015-05-20",
    "duration":18,
    "workdays":13
  },
  {
    "task":"mandarin",
    "product":1,
    "coms":1,
    "digital":0.142857143,
    "design":1.142857143,
    "video":0,
    "external":0,
    "sdate":"2015-05-02",
    "edate":"2015-05-12",
    "duration":10,
    "workdays":7
  },
  {
    "task":"zingerific",
    "product":1.75,
    "coms":1.75,
    "digital":0,
    "design":0,
    "video":0,
    "external":0,
    "sdate":"2015-05-02",
    "edate":"2015-05-07",
    "duration":5,
    "workdays":4
  },
  {
    "task":"grapefruit",
    "product":3,
    "coms":3,
    "digital":2,
    "design":14,
    "video":0,
    "external":0,
    "sdate":"2015-05-02",
    "edate":"2015-05-04",
    "duration":2,
    "workdays":1
  },
  {
    "task":"banana",
    "product":3.904761905,
    "coms":3.904761905,
    "digital":11.66666667,
    "design":5.952380952,
    "video":2.571428571,
    "external":0,
    "sdate":"2015-05-01",
    "edate":"2015-05-31",
    "duration":30,
    "workdays":21
  },
  {
    "task":"tangello",
    "product":10,
    "coms":10,
    "digital":0.4375,
    "design":1.25,
    "video":3.75,
    "external":0,
    "sdate":"2015-05-01",
    "edate":"2015-05-22",
    "duration":21,
    "workdays":16
  },
  {
    "task":"kiwi",
    "product":1.25,
    "coms":1.25,
    "digital":0,
    "design":0,
    "video":0,
    "external":0,
    "sdate":"2015-05-01",
    "edate":"2015-05-06",
    "duration":5,
    "workdays":4
  },
  {
    "task":"apple",
    "product":3.5,
    "coms":3.5,
    "digital":0,
    "design":1,
    "video":0.5,
    "external":3.5,
    "sdate":"2015-05-01",
    "edate":"2015-05-04",
    "duration":3,
    "workdays":2
  },
  {
    "task":"eggs",
    "product":5,
    "coms":5,
    "digital":2,
    "design":7,
    "video":0,
    "external":0,
    "sdate":"2015-05-01",
    "edate":"2015-05-03",
    "duration":2,
    "workdays":1
  },
  {
    "task":"mcdonalds",
    "product":0.053030303,
    "coms":0.053030303,
    "digital":0.007575758,
    "design":0.060606061,
    "video":0,
    "external":0,
    "sdate":"2015-04-30",
    "edate":"2015-10-30",
    "duration":183,
    "workdays":132
  },
  {
    "task":"danny",
    "product":0.828282828,
    "coms":0.828282828,
    "digital":2.474747475,
    "design":1.262626263,
    "video":0.545454545,
    "external":0,
    "sdate":"2015-04-30",
    "edate":"2015-09-15",
    "duration":138,
    "workdays":99
  }
];