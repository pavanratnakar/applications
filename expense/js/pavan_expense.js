// <![CDATA[
var pavan_expense =
{
	ready: function() {
		pavan_expense.theme.init();
		pavan_expense.menu.resize();
		pavan_expense.jqgrid.init()
		pavan_expense.menu.init();
	},
	theme :
	{
        width:760,
        date_width:100,
        category_width:120,
        amount_width:80,
        username_width:150,
        payment_width:100,
        note_width:140,
		init : function()
		{
			this.switcher();
		},
		switcher : function()
		{
			$('#switcher').themeswitcher();
		}
	},
	menu :
	{
		menuId : "#west-grid",
		tabId : "#tabs",
		rightPane : "#RightPane",
		mainTab : "",
		xml : "tree.xml",
		xmlPath : "xml/",
		caption : "Expense Module",
		init : function()
		{
			this.tab();
		},
		resize : function()
		{
			$('body').layout({
				resizerClass: 'ui-state-default',
				west__onresize: function (pane, $Pane) 
				{
					jQuery(pavan_expense.menu.menuId).jqGrid('setGridWidth',$Pane.innerWidth()-2);
				}
			});
		},
		tab: function()
		{
			pavan_expense.menu.maintab =$(pavan_expense.menu.tabId,pavan_expense.menu.rightPane).tabs(
			{
				add: function(e, ui) 
				{
					$(ui.tab).parents('li:first')
						.append('<span class="ui-tabs-close ui-icon ui-icon-close" title="Close Tab"></span>')
						.find('span.ui-tabs-close')
						.click(function() {
							pavan_expense.menu.maintab.tabs('remove', $('li', pavan_expense.menu.maintab).index($(this).parents('li:first')[0]));
						});
					pavan_expense.menu.maintab.tabs('select', '#' + ui.panel.id);
				}
			});
			$(pavan_expense.menu.menuId).jqGrid({
				url: pavan_expense.menu.xmlPath+pavan_expense.menu.xml,
				datatype: "xml",
				height: "auto",
				pager: false,
				loadui: "disable",
				colNames: ["id","Items","url"],
				colModel: [
					{name: "id",width:1,hidden:true, key:true},
					{name: "menu", width:150, resizable: false, sortable:false},
					{name: "url",width:1,hidden:true}
				],
				treeGrid: true,
				caption: pavan_expense.menu.caption,
				ExpandColumn: "menu",
				autowidth: true,
				rowNum: 200,
				ExpandColClick: true,
				treeIcons: {leaf:'ui-icon-document-b'},
				onSelectRow: function(rowid) 
				{
					var treedata = $(pavan_expense.menu.menuId).jqGrid('getRowData',rowid);
					if(treedata.isLeaf=="true")
					{
						var st = "#t"+treedata.id;
						if($(st).html() != null )
						{
							pavan_expense.menu.maintab.tabs('select',st);
						}
						else
						{
							pavan_expense.menu.maintab.tabs('add',st, treedata.menu);
							$(st,"#tabs").load(treedata.url);
						}
					}
				}
			});
		}
	},
	jqgrid : 
	{
		init	:	function()
		{
			this.default();
		},	
		default	:	function()
		{
			$.jgrid.defaults = $.extend($.jgrid.defaults,{loadui:"enable"});
		},
		expense	:
		{
			id		            :	"#expense",
			page	            :	"#p_expense",
            select              :   "#expense_select",
			init	:	function()
			{
				this.setup();
				this.crud(pavan_expense.jqgrid.expense.id,pavan_expense.jqgrid.expense.page);
                /// SELECT ///
                pavan_expense.jqgrid.selectChange.selectcontainer = pavan_expense.jqgrid.expense.select+"_container";
                pavan_expense.jqgrid.selectChange.selectArray = {"expense_date":"Expense Date","category_name":"Categroy","subcategory_name":"Sub Category","for_username":"Person","payment_id":"Payment","by_username":"Logged User"};
                pavan_expense.jqgrid.selectChange.selectDiv = pavan_expense.jqgrid.expense.select;
                pavan_expense.jqgrid.selectChange.grid =  pavan_expense.jqgrid.expense.id;
                pavan_expense.jqgrid.selectChange.setup();
                /// SELECT ///
                /// DATE ///
                pavan_expense.jqgrid.expense.dateRangeSelect();
                /// DATE ///
			},
			setup	:	function()
			{
				this.categories=$.ajax({url: $.url+"/expense/controller/categoryController.php?ref=select&type=expense", dataType: "json", cache: true, async: false, success: function(data, result) {if (!result) alert('Failure to retrieve the Products.');}}).responseText;
                this.payment_categories=$.ajax({url: $.url+"/expense/controller/categoryController.php?ref=select&type=payment", dataType: "json", cache: true, async: false, success: function(data, result) {if (!result) alert('Failure to retrieve the Products.');}}).responseText;
                var category_id=this.categories.replace('"',"");
                category_id=category_id.split(":",1);
                this.sub_categories=$.ajax({url: $.url+"/expense/controller/subcategoryController.php?ref=select&category_id="+category_id, dataType: "json", cache: true, async: false, success: function(data, result) {if (!result) alert('Failure to retrieve the Products.');}}).responseText;
                this.users=$.ajax({url: $.url+"/login/controller/userController.php?ref=userList", dataType: "json", cache: true, async: false, success: function(data, result) {if (!result) alert('Failure to retrieve the Products.');}}).responseText;
                this.toDate=$(this.id+"_date_form").children('.toDate').val();
                this.fromDate=$(this.id+"_date_form").children('.fromDate').val();
				$(pavan_expense.jqgrid.expense.id).jqGrid({
					url:$.url+'/expense/controller/expenseController.php?ref=details&toDate='+this.toDate+'&fromDate='+this.fromDate,
					datatype: "json",
					height: 'auto',
					width: pavan_expense.theme.width,
					colNames:['Id', 'Date', 'Category' , 'Sub Category', 'Amount', 'Person', 'Payment Type' ,'Note' , 'Logged By'],
					colModel:[
						{name:'expense_id',index:'expense_id',hidden:true,align:'center',editable:false, sorttype:'int',key:true},
						{name:'expense_date',index:'expense_date',width:pavan_expense.theme.date_width,formoptions:{label: 'Date *'}, align:'center',editable:true,editrules: { required: true,date:true },summaryType:'count', summaryTpl : '({0}) Sum', datefmt:'yyyy-mm-dd',editoptions:{size:20,dataInit:function(el){$(el).datepicker({dateFormat:'yy-mm-dd'}); }}},
                        {name:"category_name",index:"category_name",width:pavan_expense.theme.category_width,formoptions:{label: 'Category *'},align:"center",editable: true, edittype: "select", editrules: { required: true }, editoptions: { size: 50,value: (this.categories.replace('"','')).replace('"','') } },
						{name:"subcategory_name",index:"subcategory_name",width:pavan_expense.theme.category_width,formoptions:{label: 'Sub Category *'},align:"center",editable: true, edittype: "select", editrules: { required: true }, editoptions: { size: 50,value: (this.sub_categories.replace('"','')).replace('"','') } },
						{name:'amount',index:'amount',width:pavan_expense.theme.amount_width,align:"center",formoptions:{label: 'Amount *'},editable:true,editoptions:{size:10}, editrules: { required: true,number: true } ,sorttype:'number',formatter:'number',summaryType:'sum',sorttype:'number'},
                        {name:"for_username",index:"for_username",width:pavan_expense.theme.username_width,formoptions:{label: 'Person'},align:"center",editable: true, edittype: "select", editrules: { required: true }, editoptions: { size: 71,value: (this.users.replace('"','')).replace('"','') } },
                        {name:"payment_id",index:"payment_id",width:pavan_expense.theme.payment_width,formoptions:{label: 'Payment Type'},align:"center",editable: true, edittype: "select", editrules: { required: true }, editoptions: { size: 71,value: (this.payment_categories.replace('"','')).replace('"','') } },
						{name:'note',index:'note', width:pavan_expense.theme.note_width,formoptions:{label: 'Note'},align:"center", sortable:true,editable: true,editrules: { required: false } ,edittype:"textarea", editoptions:{rows:"2",cols:"20"}},
                        {name:"by_username",index:"by_username",width:pavan_expense.theme.username_width,formoptions:{label: 'By'},align:"center",editable: false, edittype: "select", editrules: { required: true }, editoptions: { size: 71,value: (this.users.replace('"','')).replace('"','') } },
					],
					rowNum:30,
					rowList:[30,60,90,120,150,180,210,240,270,300],
					pager: pavan_expense.jqgrid.expense.page,
					sortname: 'expense_date',
					viewrecords: true,
					sortorder: "asc",
					multiselect: false,
					subGrid: false,
					caption: "Expense List",
					editurl:"controller/expenseController.php?ref=operation",
                    grouping: true,
                    groupingView : {
                        groupField : ['expense_date'],
                        groupColumnShow : [true],
                        groupText : ['<b>{0}</b>'],
                        groupCollapse : true,
                        groupOrder: ['desc'],
                        groupSummary : [true],
                        showSummaryOnHide: true,
                        groupDataSorted : true
                    },
                    footerrow: true,
                    userDataOnFooter: true
				});
			},
            /// added due to modifications
            crud : function($id,$type)
            {
            	var fn_replyResponse=function(response,postdata)
                {
                    var json=response.responseText; //in my case response text form server is "{sc:true,msg:''}"
                    var result=eval("("+json+")"); //create js object from server reponse
                    if(result.status==false)
                    {
                        alert(result.message)
                    }
                    return [result.sc,result.msg,null]; 
                }
                $($id).jqGrid('navGrid',$type, 
                {add:true, view:true, del:true,edit:true}, 
                {top:0,closeAfterEdit:true,reloadAfterSubmit:true,closeOnEscape:true,afterSubmit: fn_replyResponse,bottominfo:'* Mandatory fields.',
                    beforeShowForm: function(form) {
                        pavan_expense.jqgrid.expense.dropdown.sub_category.change();            
                }}, // edit options
                {top:0,clearAfterAdd:true,reloadAfterSubmit:true,closeOnEscape:true,afterSubmit: fn_replyResponse,bottominfo:'* Mandatory fields.', 
                    beforeShowForm: function(form) {
                        pavan_expense.jqgrid.expense.dropdown.sub_category.change();
                }}, // add options
                {top:0,reloadAfterSubmit:true,afterSubmit: fn_replyResponse,closeOnEscape:true}, // del options
                {}, // search options
                {closeOnEscape:true} 
                );
            },
            dropdown : 
            {
                sub_category : 
                {
                    category_id : "",
                    content : "",
                    change : function() {
                        $("#category_name").change(function () {
                            this.category_id=$('#category_name').val();
                            this.sub_categories=$.ajax({url: $.url+"/expense/controller/subcategoryController.php?ref=select&category_id="+this.category_id, dataType: "json", cache: true, async: false, success: function(data, result) {if (!result) alert('Failure to retrieve the Products.');}}).responseText;
                            if(this.sub_categories!=0) {
                                var sub_category=this.sub_categories.replace('"',"").replace('"',"");
                                var temp="";
                                this.content="";
                                sub_category=sub_category.split(";");
                                for(i=0;i<sub_category.length;i++) {
                                    temp=sub_category[i].split(":");
                                    this.content+='<option role="option" value="'+temp[0]+'">'+temp[1]+'</option>';
                                }
                            }
                            else {
                                this.content='<option role="option" value="">None</option>';
                            }
                            $("#subcategory_name").html(this.content);
                        });
                    }
                }
            },
            dateRangeSelect : function() {
                pavan_expense.selectDate.setup();
                $(this.id+"_date_form").beautifyform();
                $(this.id+"_date_form").submit(function()
                {
                    this.toDate=$(pavan_expense.jqgrid.expense.id+"_date_form").children('.toDate').val();
                    this.fromDate=$(pavan_expense.jqgrid.expense.id+"_date_form").children('.fromDate').val();
                    $(pavan_expense.jqgrid.expense.id).jqGrid('setGridParam',{url:$.url+"/expense/controller/expenseController.php?ref=details&toDate="+this.toDate+"&fromDate="+this.fromDate,page:1}).trigger("reloadGrid");
                    return false;
                });
            }
		},
        income : 
        {
			id		            :	"#income",
			page	            :	"#p_income",
            select              :   "#income_select",
			init	:	function()
			{
				this.setup();
				this.crud(pavan_expense.jqgrid.income.id,pavan_expense.jqgrid.income.page);
                /// SELECT ///
                pavan_expense.jqgrid.selectChange.selectcontainer = pavan_expense.jqgrid.income.select+"_container";
                pavan_expense.jqgrid.selectChange.selectArray = {"income_date":"Income Date","category_name":"Categroy","for_username":"Person"};
                pavan_expense.jqgrid.selectChange.selectDiv = pavan_expense.jqgrid.income.select;
                pavan_expense.jqgrid.selectChange.grid =  pavan_expense.jqgrid.income.id;
                pavan_expense.jqgrid.selectChange.setup();
                /// SELECT ///
                /// DATE ///
                pavan_expense.jqgrid.income.dateRangeSelect();
                /// DATE ///
			},      
			setup	:	function()
			{
				this.categories=$.ajax({url: $.url+"/expense/controller/categoryController.php?ref=select&type=income", dataType: "json", cache: true, async: false, success: function(data, result) {if (!result) alert('Failure to retrieve the Products.');}}).responseText;
                var category_id=this.categories.replace('"',"");
                category_id=category_id.split(":",1);
                this.users=$.ajax({url: $.url+"/login/controller/userController.php?ref=userList", dataType: "json", cache: true, async: false, success: function(data, result) {if (!result) alert('Failure to retrieve the Products.');}}).responseText;
                this.toDate=$(this.id+"_date_form").children('.toDate').val();
                this.fromDate=$(this.id+"_date_form").children('.fromDate').val();
				$(pavan_expense.jqgrid.income.id).jqGrid({
					url:$.url+'/expense/controller/incomeController.php?ref=details&toDate='+this.toDate+'&fromDate='+this.fromDate,
					datatype: "json",
					height: 'auto',
					width: pavan_expense.theme.width,
					colNames:['Id', 'Date', 'Category' , 'Amount', 'Person', 'Note' ],
					colModel:[
						{name:'income_id',index:'expense_id',hidden:true,align:'center',editable:false, sorttype:'int',key:true},
						{name:'income_date',index:'expense_date',width:pavan_expense.theme.date_width,formoptions:{label: 'Date *'}, align:'center',editable:true,editrules: { required: true,date:true },summaryType:'count', summaryTpl : '({0}) Sum', datefmt:'yyyy-mm-dd',editoptions:{size:20,dataInit:function(el){$(el).datepicker({dateFormat:'yy-mm-dd'}); }}},
                        {name:"category_name",index:"category_name",width:pavan_expense.theme.category_width,formoptions:{label: 'Category *'},align:"center",editable: true, edittype: "select", editrules: { required: true }, editoptions: { size: 50,value: (this.categories.replace('"','')).replace('"','') } },
						{name:'amount',index:'amount',width:pavan_expense.theme.amount_width,align:"center",formoptions:{label: 'Amount *'},editable:true,editoptions:{size:10}, editrules: { required: true,number: true } ,sorttype:'number',formatter:'number',summaryType:'sum',sorttype:'number'},
                        {name:"for_username",index:"for_username",width:pavan_expense.theme.username_width,formoptions:{label: 'Person'},align:"center",editable: true, edittype: "select", editrules: { required: true }, editoptions: { size: 71,value: (this.users.replace('"','')).replace('"','') } },
						{name:'note',index:'note', width:pavan_expense.theme.note_width,formoptions:{label: 'Note'},align:"center", sortable:true,editable: true,editrules: { required: false } ,edittype:"textarea", editoptions:{rows:"2",cols:"20"}}
					],
					rowNum:20,
					rowList:[20,40,60,80,100,120,140],
					pager: pavan_expense.jqgrid.income.page,
					sortname: 'income_date',
					viewrecords: true,
					sortorder: "asc",
					multiselect: false,
					subGrid: false,
					caption: "Income List",
					editurl:"controller/incomeController.php?ref=operation",
                    grouping: true,
                    groupingView : {
                        groupField : ['income_date'],
                        groupColumnShow : [true],
                        groupText : ['<b>{0}</b>'],
                        groupCollapse : false,
                        groupOrder: ['asc'],
                        groupSummary : [true],
                        showSummaryOnHide: true,
                        groupDataSorted : true
                    },
                    footerrow: true,
                    userDataOnFooter: true
				});
			},
            /// added due to modifications
            crud : function($id,$type)
            {
            	var fn_replyResponse=function(response,postdata)
                {
                    var json=response.responseText; //in my case response text form server is "{sc:true,msg:''}"
                    var result=eval("("+json+")"); //create js object from server reponse
                    if(result.status==false)
                    {
                        alert(result.message)
                    }
                    return [result.sc,result.msg,null]; 
                }
                $($id).jqGrid('navGrid',$type, 
                {add:true, view:true, del:true,edit:true}, 
                {top:0,closeAfterEdit:true,reloadAfterSubmit:true,closeOnEscape:true,afterSubmit: fn_replyResponse,bottominfo:'* Mandatory fields.',
                    beforeShowForm: function(form) {
                        pavan_expense.jqgrid.income.dropdown.sub_category.change();            
                }}, // edit options
                {top:0,clearAfterAdd:true,reloadAfterSubmit:true,closeOnEscape:true,afterSubmit: fn_replyResponse,bottominfo:'* Mandatory fields.', 
                    beforeShowForm: function(form) {
                        pavan_expense.jqgrid.income.dropdown.sub_category.change();
                }}, // add options
                {top:0,reloadAfterSubmit:true,afterSubmit: fn_replyResponse,closeOnEscape:true}, // del options
                {}, // search options
                {closeOnEscape:true} 
                );
            },
            dropdown : 
            {
                sub_category : 
                {
                    category_id : "",
                    content : "",
                    change : function() {
                        $("#category_name").change(function () {
                            this.category_id=$('#category_name').val();
                            this.sub_categories=$.ajax({url: $.url+"/expense/controller/subcategoryController.php?ref=select&category_id="+this.category_id, dataType: "json", cache: true, async: false, success: function(data, result) {if (!result) alert('Failure to retrieve the Products.');}}).responseText;
                            if(this.sub_categories!=0) {
                                var sub_category=this.sub_categories.replace('"',"").replace('"',"");
                                var temp="";
                                this.content="";
                                sub_category=sub_category.split(";");
                                for(i=0;i<sub_category.length;i++) {
                                    temp=sub_category[i].split(":");
                                    this.content+='<option role="option" value="'+temp[0]+'">'+temp[1]+'</option>';
                                }
                            }
                            else {
                                this.content='<option role="option" value="">None</option>';
                            }
                            $("#subcategory_name").html(this.content);
                        });
                    }
                }
            },
            dateRangeSelect : function() {
                pavan_expense.selectDate.setup();
                $(this.id+"_date_form").beautifyform();
                $(this.id+"_date_form").submit(function()
                {
                    this.toDate=$(pavan_expense.jqgrid.income.id+"_date_form").children('.toDate').val();
                    this.fromDate=$(pavan_expense.jqgrid.income.id+"_date_form").children('.fromDate').val();
                    $(pavan_expense.jqgrid.income.id).jqGrid('setGridParam',{url:$.url+"/expense/controller/incomeController.php?ref=details&toDate="+this.toDate+"&fromDate="+this.fromDate,page:1}).trigger("reloadGrid");
                    return false;
                });
            }            
        },
        loan : 
        {
			id		            :	"#loan",
			page	            :	"#p_loan",
			init	:	function()
			{
				this.setup();
				pavan_expense.jqgrid.crud(pavan_expense.jqgrid.loan.id,pavan_expense.jqgrid.loan.page);
			},      
			setup	:	function()
			{
				$(pavan_expense.jqgrid.loan.id).jqGrid({
					url:$.url+'/expense/controller/loanController.php?ref=details',
					datatype: "json",
					height: 'auto',
					width: pavan_expense.theme.width,
					colNames:['Id','Start Date','End Date','Amount','Paid','Installment','Remaining','Note'],
					colModel:[
						{name:'loan_id',index:'loan_id',hidden:true,align:'center',editable:false, sorttype:'int',key:true},
						{name:'loan_start_date',index:'loan_start_date',width:pavan_expense.theme.date_width,formoptions:{label: 'Start Date *'}, align:'center',editable:true,editrules: { required: true,date:true },summaryType:'count', summaryTpl : '({0}) Sum', datefmt:'yyyy-mm-dd',editoptions:{size:20,dataInit:function(el){$(el).datepicker({dateFormat:'yy-mm-dd'}); }}},
                        {name:'loan_end_date',index:'loan_end_date',width:pavan_expense.theme.date_width,formoptions:{label: 'End Date *'}, align:'center',editable:true,editrules: { required: true,date:true },summaryType:'count', summaryTpl : '({0}) Sum', datefmt:'yyyy-mm-dd',editoptions:{size:20,dataInit:function(el){$(el).datepicker({dateFormat:'yy-mm-dd'}); }}},
						{name:'loan_amount',index:'loan_amount',width:pavan_expense.theme.amount_width,align:"center",formoptions:{label: 'Amount *'},editable:true,editoptions:{size:10}, editrules: { required: true,number: true } ,sorttype:'number',formatter:'number',summaryType:'sum',sorttype:'number'},
                        {name:'loan_paid_amount',index:'loan_paid_amount',width:pavan_expense.theme.amount_width,align:"center",formoptions:{label: 'Paid Amount *'},editable:true,editoptions:{size:10}, editrules: { required: true,number: true } ,sorttype:'number',formatter:'number',summaryType:'sum',sorttype:'number'},
                        {name:'monthly_installment',index:'monthly_installment',width:pavan_expense.theme.amount_width,align:"center",formoptions:{label: 'Monthly Installment *'},editable:true,editoptions:{size:10}, editrules: { required: true,number: true } ,sorttype:'number',formatter:'number',summaryType:'sum',sorttype:'number'},
                        {name:'loan_remaining_amount',index:'loan_remaining_amount',width:pavan_expense.theme.amount_width,align:"center",formoptions:{label: 'Remaining *'},editable:false ,sorttype:'number',formatter:'number',summaryType:'sum',sorttype:'number'},
						{name:'note',index:'note', width:pavan_expense.theme.note_width,formoptions:{label: 'Note'},align:"center", sortable:true,editable: true,editrules: { required: false } ,edittype:"textarea", editoptions:{rows:"2",cols:"20"}}
					],
					rowNum:20,
					rowList:[20,40,60,80,100,120,140],
					pager: pavan_expense.jqgrid.loan.page,
					sortname: 'loan_amount',
					viewrecords: true,
					sortorder: "asc",
					multiselect: false,
					subGrid: false,
					caption: "Loan List",
					editurl:"controller/loanController.php?ref=operation",
                    grouping: false,
                    groupingView : {
                        groupField : ['loan_id'],
                        groupColumnShow : [true],
                        groupText : ['<b>{0}</b>'],
                        groupCollapse : false,
                        groupOrder: ['asc'],
                        groupSummary : [true],
                        showSummaryOnHide: true,
                        groupDataSorted : true
                    },
                    footerrow: true,
                    userDataOnFooter: true
				});
			}
        },
		category	:
		{
			id		:	"#category",
			page	:	"#p_category",
			init	:	function()
			{
				this.setup();
				pavan_expense.jqgrid.crud(pavan_expense.jqgrid.category.id,pavan_expense.jqgrid.category.page);
			},
			setup	:	function()
			{
				$(pavan_expense.jqgrid.category.id).jqGrid({
					url:$.url+'/expense/controller/categoryController.php?ref=details&type=expense',
					datatype: "json",
					height: 'auto',
					width: pavan_expense.theme.width,
					colNames:['Id', 'Category'],
					colModel:[
						{name:'category_id',index:'category_id', hidden:true,width:40, align:'center', editable:false,key:true},
						{name:'category_name',index:'category_name', formoptions:{label: 'Category *'},width:pavan_expense.theme.category_width,align:'center',sortable: true, editable: true, editrules: { required: true } }
					],
					rowNum:20,
					rowList:[20,40,60,80,100,120,140],
					pager: pavan_expense.jqgrid.category.page,
					sortname: 'category_name',
					viewrecords: true,
					sortorder: "asc",
					multiselect: false,
					subGrid: false,
					caption: "Category List",
					editurl:"controller/categoryController.php?ref=operation&type=expense",
					grouping: false,
					groupingView : {},
					footerrow: false,
					userDataOnFooter: false
				});
			}
		},
        income_category	:
		{
			id		:	"#income_category",
			page	:	"#p_income_category",
			init	:	function()
			{
				this.setup();
				pavan_expense.jqgrid.crud(pavan_expense.jqgrid.income_category.id,pavan_expense.jqgrid.income_category.page);
			},
			setup	:	function()
			{
				$(pavan_expense.jqgrid.income_category.id).jqGrid({
					url:$.url+'/expense/controller/categoryController.php?ref=details&type=income',
					datatype: "json",
					height: 'auto',
					width: pavan_expense.theme.width,
					colNames:['Id', 'Category'],
					colModel:[
						{name:'category_id',index:'category_id', hidden:true,width:40, align:'center', editable:false,key:true},
						{name:'category_name',index:'category_name', formoptions:{label: 'Category *'},width:pavan_expense.theme.category_width,align:'center',sortable: true, editable: true, editrules: { required: true } }
					],
					rowNum:20,
					rowList:[20,40,60,80,100,120,140],
					pager: pavan_expense.jqgrid.income_category.page,
					sortname: 'category_name',
					viewrecords: true,
					sortorder: "asc",
					multiselect: false,
					subGrid: false,
					caption: "Income Category List",
					editurl:"controller/categoryController.php?ref=operation&type=income",
					grouping: false,
					groupingView : {},
					footerrow: false,
					userDataOnFooter: false
				});
			}
		},
        payment_category	:
		{
			id		:	"#payment_category",
			page	:	"#p_payment_category",
			init	:	function()
			{
				this.setup();
				pavan_expense.jqgrid.crud(pavan_expense.jqgrid.payment_category.id,pavan_expense.jqgrid.payment_category.page);
			},
			setup	:	function()
			{
				$(pavan_expense.jqgrid.payment_category.id).jqGrid({
					url:$.url+'/expense/controller/categoryController.php?ref=details&type=payment',
					datatype: "json",
					height: 'auto',
					width: pavan_expense.theme.width,
					colNames:['Id', 'Category','Accountable'],
					colModel:[
						{name:'category_id',index:'category_id', hidden:true,width:40, align:'center', editable:false,key:true},
						{name:'category_name',index:'category_name', formoptions:{label: 'Category *'},width:pavan_expense.theme.category_width,align:'center',sortable: true, editable: true, editrules: { required: true } },
                        {name:'accountable',index:'accountable', formoptions:{label: 'Accountable'},width:pavan_expense.theme.category_width,align:'center',sortable: true, editable: true, edittype:"checkbox", formatter:'checkbox',editoptions: { value:"1:0" } }
					],
					rowNum:20,
					rowList:[20,40,60,80,100,120,140],
					pager: pavan_expense.jqgrid.payment_category.page,
					sortname: 'category_name',
					viewrecords: true,
					sortorder: "asc",
					multiselect: false,
					subGrid: false,
					caption: "Payment Category List",
					editurl:"controller/categoryController.php?ref=operation&type=payment",
					grouping: false,
					groupingView : {},
					footerrow: false,
					userDataOnFooter: false
				});
			}
		},
		subcategory	:
		{
			id		:	"#subcategory",
			page	:	"#p_subcategory",
			categories : "",
			init	:	function()
			{
				this.setup();
				pavan_expense.jqgrid.crud(pavan_expense.jqgrid.subcategory.id,pavan_expense.jqgrid.subcategory.page);
			},
			setup	:	function()
			{
				this.categories=$.ajax({url: $.url+"/expense/controller/categoryController.php?ref=select&type=expense", dataType: "json", cache: true, async: false, success: function(data, result) {if (!result) alert('Failure to retrieve the Products.');}}).responseText;
				$(pavan_expense.jqgrid.subcategory.id).jqGrid({
					url:$.url+'/expense/controller/subcategoryController.php?ref=details',
					datatype: "json",
					height: 'auto',
					width: pavan_expense.theme.width,
					colNames:['Id', 'Sub Category','Category'],
					colModel:[
						{name:'subcategory_id',index:'subcategory_id', hidden:true,width:40, align:'center', editable:false,key:true},
						{name:'subcategory_name',index:'subcategory_name', formoptions:{label: 'Sub Category *'},width:pavan_expense.theme.category_width,align:'center',sortable: true, editable: true, editrules: { required: true } },
						{name:"category_id",index:"category_name",formoptions:{label: 'Category *'},width:pavan_expense.theme.category_width,align:"center",editable: true, edittype: "select", editrules: { required: true }, editoptions: { size: 71,value: (this.categories.replace('"','')).replace('"','') } },
					],
					rowNum:20,
					rowList:[20,40,60,80,100,120,140],
					pager: pavan_expense.jqgrid.subcategory.page,
					sortname: 'subcategory_name',
					viewrecords: true,
					sortorder: "asc",
					multiselect: false,
					subGrid: false,
					caption: "Sub Category List",
					editurl:"controller/subcategoryController.php?ref=operation",
					grouping: false,
					groupingView : {},
					footerrow: false,
					userDataOnFooter: false
				});
			}
		},
		crud : function($id,$type)
		{
			$($id).jqGrid('navGrid',$type, 
			{add:true, view:true, del:true,edit:true}, 
			{top:0,closeAfterEdit:true,reloadAfterSubmit:true,closeOnEscape:true,bottominfo:'* Mandatory fields.'}, // edit options
			{top:0,clearAfterAdd:true,reloadAfterSubmit:true,closeOnEscape:true,bottominfo:'* Mandatory fields.'}, // add options
			{top:0,reloadAfterSubmit:true,closeOnEscape:true}, // del options
			{}, // search options
			{closeOnEscape:true} 
			);
		},
        selectChange :
        {
            selectcontainer : "",
            selectArray : "",
            selectDiv : "",
            grid : "",
            setup : function()
            {
                $(this.selectcontainer).html('Group By: <select id="'+this.selectDiv.replace('#','')+'"></select>');
                for (key in this.selectArray) {
                    $(this.selectDiv).append('<option value="' + key + '">' + this.selectArray[key] + '</option>');
                }
                $(this.selectDiv).append('<option value="clear">Remove Grouping</option>');
                this.clickBinding();
            },
            clickBinding : function()
            {
                $(pavan_expense.jqgrid.selectChange.selectDiv).change(function()
                {
                    var vl = $(this).val();
                    if(vl) 
                    {
                        if(vl == "clear") {
                            $(pavan_expense.jqgrid.selectChange.grid).jqGrid('groupingRemove',true);
                        } else {
                            $(pavan_expense.jqgrid.selectChange.grid).jqGrid('groupingGroupBy',vl);
                        }
                    }
                });
            
            }
        }
	},
    visulize :
    {
        width : '640px',
        category : 
        {
        	id              :   "#visualize_category",
            select          :   "#visualize_category_select",
            type            :   "",
            caption         :   "",
            init : function()
            {
                /// DATE ///
                pavan_expense.visulize.dateRangeSelect.init(this.id);
                /// DATE ///
                pavan_expense.visulize.category.type=$(this.id+'_select').val();
                pavan_expense.visulize.category.setup();
            },
            setup : function()
            {
                this.caption='Category Statistics';
                $(this.id+'_table caption').html(this.caption);
                $.ajax({
                    url: $.url+'/expense/controller/expenseController.php?ref=visulize_category&toDate='+pavan_expense.visulize.dateRangeSelect.toDate+'&fromDate='+pavan_expense.visulize.dateRangeSelect.fromDate+'&jsoncallback=?',
                    dataType: "json",
                    cache: true,
                    type: "GET",
                    success:function(data) {
                        var temp='';
                        var row_id='';
                        $(pavan_expense.visulize.category.id+'_table thead tr').empty();
                        $(pavan_expense.visulize.category.id+'_table tbody').empty();
                        $(pavan_expense.visulize.category.id+'_table thead tr').append('<th scope="col">Category</th>');
                        for (key in data) {
                            $(pavan_expense.visulize.category.id+'_table tbody').append('<tr><th scope="row">' + data[key]['name'] + '</th><td>' + data[key]['amount'] + '</td></tr>');
                        }
                        if( $(pavan_expense.visulize.category.id+' .visualize').get(0) ) {
                            $(pavan_expense.visulize.category.id+' .visualize').trigger('visualizeRefresh');
                        }
                        else {
                            $(pavan_expense.visulize.category.id+'_table').visualize({type: 'pie', height: '300px', width:  pavan_expense.visulize.width});
                            $(pavan_expense.visulize.category.id+'_table').visualize({type: 'bar', width: pavan_expense.visulize.width});
                            //$(pavan_expense.visulize.category.id+'_table').visualize({type: 'area', width: pavan_expense.visulize.width});
                            //$(pavan_expense.visulize.category.id+'_table').visualize({type: 'line', width: pavan_expense.visulize.width});
                        }
                    }
                });
            }
        },
        sub_category : 
        {
        	id              :   "#visualize_sub_category",
            select          :   "#visualize_sub_category_select",
            type            :   "",
            caption         :   "",
            init : function()
            {
                /// SELECT ///
                pavan_expense.jqgrid.selectChange.selectcontainer = pavan_expense.visulize.sub_category.select+"_container";
                pavan_expense.jqgrid.selectChange.selectArray = {"category":"Categroy","sub_category":"Sub Category","user":"Person"};
                pavan_expense.jqgrid.selectChange.selectDiv = pavan_expense.visulize.sub_category.select;
                pavan_expense.jqgrid.selectChange.setup();
                /// SELECT ///
                /// DATE ///
                pavan_expense.visulize.dateRangeSelect.init(this.id);
                /// DATE ///
                pavan_expense.visulize.sub_category.type=$(this.id+'_select').val();
                pavan_expense.visulize.sub_category.setup();
            },
            setup : function()
            {
                this.caption='Sub category Statistics';
                $(this.id+'_table caption').html(this.caption);
                $.ajax({
                    url: $.url+'/expense/controller/expenseController.php?ref=visulize_sub_category&toDate='+pavan_expense.visulize.dateRangeSelect.toDate+'&fromDate='+pavan_expense.visulize.dateRangeSelect.fromDate+'&jsoncallback=?',
                    dataType: "json",
                    cache: true,
                    type: "GET",
                    success:function(data) {
                        var temp='';
                        var row_id='';
                        $(pavan_expense.visulize.sub_category.id+'_table thead tr').empty();
                        $(pavan_expense.visulize.sub_category.id+'_table tbody').empty();
                        $(pavan_expense.visulize.sub_category.id+'_table thead tr').append('<th scope="col">Sub Category</th>');
                        for (key in data) {
                            $(pavan_expense.visulize.sub_category.id+'_table tbody').append('<tr><th scope="row">' + data[key]['name'] + '</th><td>' + data[key]['amount'] + '</td></tr>');
                        }
                        if( $(pavan_expense.visulize.sub_category.id+' .visualize').get(0) ) {
                            $(pavan_expense.visulize.sub_category.id+' .visualize').trigger('visualizeRefresh');
                        }
                        else {
                            $(pavan_expense.visulize.sub_category.id+'_table').visualize({type: 'pie', height: '300px', width: pavan_expense.visulize.width});
                            $(pavan_expense.visulize.sub_category.id+'_table').visualize({type: 'bar', width: pavan_expense.visulize.width});
                        }
                    }
                });
            }
        },
        user : 
        {
        	id              :   "#visualize_user",
            select          :   "#visualize_user_select",
            type            :   "",
            caption         :   "",
            init : function()
            {
                /// DATE ///
                pavan_expense.visulize.dateRangeSelect.init(this.id);
                /// DATE ///
                pavan_expense.visulize.user.type=$(this.id+'_select').val();
                pavan_expense.visulize.user.setup();
            },
            setup : function()
            {
                this.caption='User Statistics';
                $(this.id+'_table caption').html(this.caption);
                $.ajax({
                    url: $.url+'/expense/controller/expenseController.php?ref=visulize_user&toDate='+pavan_expense.visulize.dateRangeSelect.toDate+'&fromDate='+pavan_expense.visulize.dateRangeSelect.fromDate+'&jsoncallback=?',
                    dataType: "json",
                    cache: true,
                    type: "GET",
                    success:function(data) {
                        var temp='';
                        var row_id='';
                        $(pavan_expense.visulize.user.id+'_table thead tr').empty();
                        $(pavan_expense.visulize.user.id+'_table tbody').empty();
                        $(pavan_expense.visulize.user.id+'_table thead tr').append('<th scope="col">Person</th>');
                        for (key in data) {
                            $(pavan_expense.visulize.user.id+'_table tbody').append('<tr><th scope="row">' + data[key]['name'] + '</th><td>' + data[key]['amount'] + '</td></tr>');
                        }
                        if( $(pavan_expense.visulize.user.id+' .visualize').get(0) ) {
                            $(pavan_expense.visulize.user.id+' .visualize').trigger('visualizeRefresh');
                        }
                        else {
                            $(pavan_expense.visulize.user.id+'_table').visualize({type: 'pie', height: '300px', width:  pavan_expense.visulize.width});
                            $(pavan_expense.visulize.user.id+'_table').visualize({type: 'bar', width: pavan_expense.visulize.width});
                        }
                    }
                });
            },
        },
        payment :
        {
        	id              :   "#visualize_payment",
            select          :   "#visualize_payment_select",
            type            :   "",
            caption         :   "",
            init : function()
            {
                /// DATE ///
                pavan_expense.visulize.dateRangeSelect.init(this.id);
                /// DATE ///
                pavan_expense.visulize.payment.type=$(this.id+'_select').val();
                pavan_expense.visulize.payment.setup();
            },
            setup : function()
            {
                this.caption='Payment Statistics';
                $(this.id+'_table caption').html(this.caption);
                $.ajax({
                    url: $.url+'/expense/controller/expenseController.php?ref=visulize_payment&toDate='+pavan_expense.visulize.dateRangeSelect.toDate+'&fromDate='+pavan_expense.visulize.dateRangeSelect.fromDate+'&jsoncallback=?',
                    dataType: "json",
                    cache: true,
                    type: "GET",
                    success:function(data) {
                        var temp='';
                        var row_id='';
                        $(pavan_expense.visulize.payment.id+'_table thead tr').empty();
                        $(pavan_expense.visulize.payment.id+'_table tbody').empty();
                        $(pavan_expense.visulize.payment.id+'_table thead tr').append('<th scope="col">Payment</th>');
                        for (key in data) {
                            $(pavan_expense.visulize.payment.id+'_table tbody').append('<tr><th scope="row">' + data[key]['name'] + '</th><td>' + data[key]['amount'] + '</td></tr>');
                        }
                        if( $(pavan_expense.visulize.payment.id+' .visualize').get(0) ) {
                            $(pavan_expense.visulize.payment.id+' .visualize').trigger('visualizeRefresh');
                        }
                        else {
                            $(pavan_expense.visulize.payment.id+'_table').visualize({type: 'pie', height: '300px', width:  pavan_expense.visulize.width});
                            $(pavan_expense.visulize.payment.id+'_table').visualize({type: 'bar', width: pavan_expense.visulize.width});
                        }
                    }
                });
            },
        },
        logged_user : 
        {
        	id              :   "#visualize_logged_user",
            select          :   "#visualize_logged_user_select",
            type            :   "",
            caption         :   "",
            init : function()
            {
                /// DATE ///
                pavan_expense.visulize.dateRangeSelect.init(this.id);
                /// DATE ///
                pavan_expense.visulize.logged_user.type=$(this.id+'_select').val();
                pavan_expense.visulize.logged_user.setup();
            },
            setup : function()
            {
                this.caption='Logged User Statistics';
                $(this.id+'_table caption').html(this.caption);
                $.ajax({
                    url: $.url+'/expense/controller/expenseController.php?ref=visulize_logged_user&toDate='+pavan_expense.visulize.dateRangeSelect.toDate+'&fromDate='+pavan_expense.visulize.dateRangeSelect.fromDate+'&jsoncallback=?',
                    dataType: "json",
                    cache: true,
                    type: "GET",
                    success:function(data) {
                        var temp='';
                        var row_id='';
                        $(pavan_expense.visulize.logged_user.id+'_table thead tr').empty();
                        $(pavan_expense.visulize.logged_user.id+'_table tbody').empty();
                        $(pavan_expense.visulize.logged_user.id+'_table thead tr').append('<th scope="col">Logged User</th>');
                        for (key in data) {
                            $(pavan_expense.visulize.logged_user.id+'_table tbody').append('<tr><th scope="row">' + data[key]['name'] + '</th><td>' + data[key]['amount'] + '</td></tr>');
                        }
                        if( $(pavan_expense.visulize.logged_user.id+' .visualize').get(0) ) {
                            $(pavan_expense.visulize.logged_user.id+' .visualize').trigger('visualizeRefresh');
                        }
                        else {
                            $(pavan_expense.visulize.logged_user.id+'_table').visualize({type: 'pie', height: '300px', width:  pavan_expense.visulize.width});
                            $(pavan_expense.visulize.logged_user.id+'_table').visualize({type: 'bar', width: pavan_expense.visulize.width});
                        }
                    }
                });
            },
        },
        dateRangeSelect : 
        {
            toDate : "",
            fromDate : "",
            init : function(id) {
                pavan_expense.selectDate.setup();
                $(id+"_date_form").beautifyform();
                this.toDate=$(id+"_date_form").children('.toDate').val();
                this.fromDate=$(id+"_date_form").children('.fromDate').val();
                $(id+"_date_form").submit(function()
                {
                    
                    eval('pavan_expense.visulize.'+id.replace('#visualize_','')+'.init()');                   
                    return false;
                });
            }
        }
    },
    selectDate : 
    {
        dateFormat : 'yy-mm-dd',
        setup : function()
        {
            $( ".datePicker" ).datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange: '1900:2011',
                showAnim: 'bounce',
                dateFormat:this.dateFormat
            });
        }
    }
}
/// CUSTORM JQUERY FUNCTIONS
$.fn.beautifyform = function(){
     $(this).children("input:submit").button();
}
/// CUSTORM JQUERY FUNCTIONS
$(pavan_expense.ready);
// ]]>