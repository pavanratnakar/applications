<?php
class Config
{
	/* DB CONFIG */
	static $db_server='localhost';
	static $db_username='pavanrat_app';
	static $db_password='9vPh_v_S~cOi';
	static $db_database='pavanrat_applications';
	/* DB CONFIG */
	
	/* USERS */
	static $application_users='application_users';
    static $application_users_attributes='application_users_attributes';
	static $application_active_users='application_active_users';
	/* USERS */

	/* EXPENSE */
	static $session_name="pavan_applications";
	static $session_expiry="86400"; /* (1 day = 1*24*60*60) */
	static $expense_category='expense_category';
	static $expense_sub_category='expense_subcategory';
	static $expense_main='expense_main';
    static $income_category='income_category';
    static $income_main='income_main';
    static $loan_main='loan_main';
    static $statistics_max_range='6';
    static $payment_category='payment_category';
	/* EXPENSE */
	
	/* APPLICATIONS */
	static $applications="applications";
	/* APPLICATIONS */
	
}
?>