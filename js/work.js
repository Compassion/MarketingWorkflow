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
        //this.buildHighChart("hc");
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