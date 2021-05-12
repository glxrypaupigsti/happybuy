

var scriptTag = document.getElementById('scriptTag');

var shoproot = location.pathname.substr(0, location.pathname.lastIndexOf('/') + 1);

window.UMEDITOR_HOME_URL = shoproot + 'static/script/umeditor/';

var shopdomain = location.hostname;

// datatable 配置项
var DataTableConfig = {
    "bPaginate": true,
    "bLengthChange": false,
    "iDisplayLength": 10,
    "bFilter": false,
    "bSort": false, //排序功能
    "bInfo": false,
    "bAutoWidth": false,
    "bJQueryUI": false,
    /*"pagingType": "full_numbers",*/
    "scrollX":false,
    "fnInitComplete": function () {
        dataTableLis();
        $('.dataTables_filter').addClass('clearfix');
        $('.search-w-box input').attr('placeholder', '输入搜索内容');
    },
    "oLanguage": {
        "sLengthMenu": "每页显示 _MENU_条",
        "sZeroRecords": "没有找到符合条件的数据",
        "sProcessing": "<img src=’./loading.gif’ />",
        "sInfo": "当前第 _START_ - _END_ 条　共计 _TOTAL_ 条",
        "sInfoEmpty": "木有记录",
        "sInfoFiltered": "(从 _MAX_ 条记录中过滤)",
        "sSearch": "搜索：",
    	"oPaginate": {
        	"sFirst": "首页",
        	"sPrevious": "前一页",
        	"sNext": "后一页",
        	"sLast": "尾页"
        }
    }
};

if (scriptTag) {
    require.config({
        packages: [
            {
                name: 'echarts',
                location: './Echarts/src',
                main: 'echarts'
            },
            {
                name: 'zrender',
                location: './zrender/src', // zrender与echarts在同一级目录
                main: 'zrender'
            }
        ]
    });
    require.config({
        paths: {
            jquery: 'jquery-2.1.1.min',
            util: 'Wdmin/util',
            Spinner: 'spin.min',
            highcharts: 'highcharts/js/highcharts',
            fancyBox: 'fancyBox/source/jquery.fancybox.pack',
            datatables: 'DataTables/media/js/jquery.dataTables.min',
            datatables_bootstrap: 'DataTables/media/js/dataTables.bootstrap.min',
            provinceCity: 'provinceCity',
            jUploader: 'jUploader.min',
            jPrintArea: 'jquery.PrintArea',
            ztree: 'zTree_v3/js/jquery.ztree.core-3.5.min',
            ztree_loader: 'Wdmin/ztree_loader',
            WdatePicker: 'My97DatePicker/WdatePicker',
            ueditor: 'umeditor/umeditor.min',
            ueditor_config: 'umeditor/umeditor.config',
            pagination: 'lib/jquery.pagination.min',
            jpaginate: 'lib/jpaginate',
            datetimepicker: 'lib/jquery.datetimepicker.full.min',
            baiduTemplate: 'lib/baiduTemplate',
            //加载layui的相关js
            layer: 'layui/layer/layer',
            laypage: 'layui/laypage/laypage',
            laytpl: 'layui/laytpl/laytpl',
            laydate: 'layui/laydate/laydate',
            
            page_orders_all: 'Wdmin/orders/orders_all',
            page_orders_toexpress: 'Wdmin/orders/orders_toexpress',
            page_orders_expressing: 'Wdmin/orders/orders_expressing',
            page_orders_toreturn: 'Wdmin/orders/orders_toreturn',
            page_home: 'Wdmin/stat_center/home',
            page_list_products: 'Wdmin/products/list_products',
            page_alter_products_categroy: 'Wdmin/products/alter_products_categroy',
            page_alter_categroy: 'Wdmin/products/alter_categroy',
            page_iframe_list_products: 'Wdmin/products/iframe_list_products',
            page_iframe_alter_product: 'Wdmin/products/iframe_alter_product',
            page_list_customers: 'Wdmin/customers/list_customers',
            page_deleted_products: 'Wdmin/products/deleted_products',
            page_alter_product_specs: 'Wdmin/products/alter_product_specs',
            page_list_customer_orders: 'Wdmin/customers/list_customer_orders',
            page_list_companys: 'Wdmin/company/list_companys',
            page_company_requests: 'Wdmin/company/company_requests',
            page_alter_product_serials: 'Wdmin/products/alter_product_serials',
            page_alter_products_brand: 'Wdmin/products/page_alter_products_brand',
            page_stocks_products: 'Wdmin/stocks/products_stock',
            page_add_product_stock: 'Wdmin/stocks/add_product_stock',
            page_alert_share: 'Wdmin/share/alert_share',
            page_share_list: 'Wdmin/share/ajax_share_list',
            page_stocks_ingredients: 'Wdmin/stocks/ingredients_stock',
            page_add_ingredient: 'Wdmin/stocks/add_ingredient',
            page_checkin_ingredient: 'Wdmin/stocks/checkin_ingredient',
            page_checkout_ingredient: 'Wdmin/stocks/checkout_ingredient',
            page_writedown_ingredient: 'Wdmin/stocks/writedown_ingredient',
            page_ingredient_changelog: 'Wdmin/stocks/ingredient_changelog',
            page_packs_products: 'Wdmin/stocks/packs_stock',
        },
        shim: {
            'page_home': {
                deps: ['jquery', 'highcharts']
            },
            'page_orders_all': {
                deps: ['jquery', 'datatables']
            },
            'page_orders_toexpress': {
                deps: ['jquery', 'datatables']
            },
            'page_orders_expressing': {
                deps: ['jquery', 'datatables']
            },
            'page_orders_toreturn': {
                deps: ['jquery', 'datatables']
            },
            'page_list_products': {
                deps: ['jquery', 'datatables']
            },
            'page_alter_products_categroy': {
                deps: ['jquery', 'datatables', 'ztree']
            },
            'page_alter_categroy': {
                deps: ['jquery', 'datatables', 'ztree', 'jUploader']
            },
            'page_iframe_list_products': {
                deps: ['jquery', 'datatables', 'ztree']
            },
            'page_iframe_alter_product': {
                deps: ['jquery', 'datatables', 'ztree', 'ueditor', 'jUploader']
            },
            'page_alter_products_brand': {
                deps: ['jquery']
            },
            'page_list_customers': {
                deps: ['jquery', 'datatables', 'ztree', 'ueditor']
            },
            'page_deleted_products': {
                deps: ['jquery', 'datatables', 'ztree', 'ueditor']
            },
            'page_alter_product_specs': {
                deps: ['jquery']
            },
            'page_list_customer_orders': {
                deps: ['jquery']
            },
            'page_list_companys': {
                deps: ['jquery']
            },
            'page_company_requests': {
                deps: ['jquery']
            },
            'page_alter_product_serials': {
                deps: ['jquery']
            },
            'page_stocks_products': {
                deps: ['jquery', 'datatables']
            },
            'page_add_product_stock': {
                deps: ['jquery', 'datatables']
            },
            'page_alert_share': {
                deps: ['jquery']
            },
            'page_share_list': {
                deps: ['jquery', 'datatables']
            },
            'page_stocks_ingredients': {
                deps: ['jquery', 'datatables']
            },
            'page_add_ingredient': {
                deps: ['jquery', 'datatables']
            },
            'page_checkin_ingredient': {
                deps: ['jquery', 'datatables']
            },
            'page_checkout_ingredient': {
                deps: ['jquery', 'datatables']
            },
            'page_writedown_ingredient': {
                deps: ['jquery', 'datatables']
            },
            'page_ingredient_changelog': {
                deps: ['jquery', 'datatables']
            },
            'page_stocks_packs': {
                deps: ['jquery', 'datatables']
            },
            'fancyBox': {
                deps: ['jquery']
            },
            'jUploader': {
                deps: ['jquery']
            },
            'layer':{
            	deps: ['jquery'],
            	exports: 'layer'
            },
            'laypage':{
            	deps: ['jquery'],
            	exports: 'laypage'
            },
            'laytpl':{
            	deps: ['jquery'],
            	exports: 'laytpl'
            },
            
            'datetimepicker': {
                deps: ['jquery'],
                exports: 'datetimepicker'
            },
            'datatables': {
                deps: ['jquery'],
                exports: 'datatable'
            },
            'datatables_bootstrap': {
            	deps: ['jquery','datatables'],
            	exports: 'datatables_bootstrap'
            },
            'provinceCity': {
                deps: ['jquery'],
                exports: 'provinceCity'
            },
            'highcharts': {
                deps: ['jquery'],
                exports: 'highcharts'
            },
            'ztree_loader': {
                deps: ['jquery', 'ztree'],
                exports: 'ztree_loader'
            },
            'ueditor': {
                deps: ['jquery', 'ueditor_config']
            },
            'ztree': {
                deps: ['jquery']
            },
            'jquery': {
                exports: '$'
            }
        },
        //urlArgs: "bust=1.5.3",
        urlArgs: "bust=" + (new Date()).getMonth().toString() + (new Date()).getDay().toString() + (new Date()).getHours().toString(),
        xhtml: true
    });

    require([scriptTag.innerHTML], function () {

    });
}