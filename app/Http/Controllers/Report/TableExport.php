<?php

namespace App\Http\Controllers\Report;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class TableExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DB::table('change_request as req')
            ->leftJoin('applications as apps', 'apps.id', '=', 'req.application_id')
            ->leftJoin('workflow_type as flow', 'flow.id', '=', 'req.workflow_type_id')
            ->leftJoin('change_request_statuses as curr_status', function($join) {
                $join->on('curr_status.cr_id', '=', 'req.id')
                     ->where('curr_status.active', 1);
            })
            ->leftJoin('statuses as stat', 'stat.id', '=', 'curr_status.new_status_id')
            ->leftJoin('group_statuses as gro_stat', 'gro_stat.status_id', '=', 'curr_status.new_status_id')
            ->leftJoin('groups as grou', 'grou.id', '=', 'gro_stat.group_id')
            ->leftJoin('group_applications as grou_apps', 'grou_apps.application_id', '=', 'req.application_id')
            ->leftJoin('groups as grou_unit', 'grou_unit.id', '=', 'grou_apps.group_id')
            ->leftJoin('units as unt', 'unt.id', '=', 'grou_unit.unit_id')
            ->leftJoin('sla_calculations as sla', 'sla.status_id', '=', 'curr_status.new_status_id')
            ->leftJoin('change_request_custom_fields as custom_field_chang', function($join) {
                $join->on('custom_field_chang.cr_id', '=', 'req.id')
                     ->where('custom_field_chang.custom_field_id', 67);
            })
            ->leftJoin('users as usr', 'usr.id', '=', 'custom_field_chang.custom_field_value')
            ->leftJoin('roles', 'roles.id', '=', 'usr.role_id')
            ->leftJoin('change_request_custom_fields as dpnd_on', function($join) {
                $join->on('dpnd_on.cr_id', '=', 'req.id')
                     ->where('dpnd_on.custom_field_name', '=', 'cr_type');
            })
             ->leftJoin('change_request_custom_fields as on_bhls', function($join) {
                $join->on('on_bhls.cr_id', '=', 'req.id')
                     ->where('on_bhls.custom_field_name', '=', 'on_behalf');
            })
            ->select(
                'req.cr_no',
                'apps.name as Applications',
                'req.title',
                'flow.name as Workflow_Type',
                DB::raw("
                    CASE 
                        WHEN req.top_management = '1' THEN 'YES' 
                        ELSE 'NO' 
                    END as Top_Management
                "),
                DB::raw("
                    CASE 
                        WHEN req.hold = '1' THEN 'YES' 
                        ELSE 'NO' 
                    END as On_Hold
                "),
                 DB::raw("
                    CASE 
                        WHEN on_bhls.custom_field_value = '1' THEN 'YES' 
                        ELSE 'NO' 
                    END as On_Behalf
                "),
                DB::raw("
                    CASE 
                        WHEN dpnd_on.custom_field_value = '1' THEN 'Normal' 
                        WHEN dpnd_on.custom_field_value = '2' THEN 'Depend On' 
                        WHEN dpnd_on.custom_field_value = '3' THEN 'Relevant' 
                        ELSE 'N/A' 
                    END as CR_Type
                "),
                DB::raw("'NA' as Vendor_Name"),
                DB::raw("GROUP_CONCAT(DISTINCT stat.status_name ORDER BY stat.status_name SEPARATOR ', ') as Current_Status"),
                DB::raw("CONCAT(sla.unit_sla_time, ' ', sla.sla_type_unit) as Assigned_SLA"),
                'req.start_design_time as Design_Estimation_Start',
                'req.end_design_time as Design_Estimation_End',
                'req.start_develop_time as Technical_Estimation_Start',
                'req.end_develop_time as Technical_Estimation_End',
                'unt.name as Unit_Name',
                'req.start_test_time as Testing_Estimation_Start',
                'req.end_test_time as Testing_Estimation_End',
                'grou.title as Current_Assigned_Group',
                'usr.user_name as Assigned_Member',
                DB::raw("'Not Found' as Assigned_Member_Level"),
                DB::raw("IFNULL(req.end_test_time, req.end_develop_time) as Expected_Delivery_date"),
                'req.requester_name',
                'req.division_manager'
            )
            ->groupBy('req.cr_no');

        // Apply filters if present
        if(!empty($this->filters['cr_type'])) {
            $query->where('req.workflow_type_id', $this->filters['cr_type']);
        }

        if(!empty($this->filters['status_ids'])) {
            $query->whereIn('curr_status.new_status_id', $this->filters['status_ids']);
        }

        if(!empty($this->filters['cr_nos'])) {
            $cr_nos_array = array_map('trim', explode(',', $this->filters['cr_nos']));
            $query->whereIn('req.cr_no', $cr_nos_array);
        }

        return $query->get();
    }

    /**
     * Add header row to the Excel file
     */
    public function headings(): array
    {
        return [
            'CR No',
            'Applications',
            'Title',
            'Workflow Type',
            'Top Management',
            'On Hold',
            'On Behalf',
            'CR Type',
            'Vendor Name',
            'Current Status',
            'Assigned SLA',
            'Design Estimation Start',
            'Design Estimation End',
            'Technical Estimation Start',
            'Technical Estimation End',
            'Unit Name',
            'Testing Estimation Start',
            'Testing Estimation End',
            'Current Assigned Group',
            'Assigned Member',
            'Assigned Member Level',
            'Expected Delivery Date',
            'Requester Name',
            'Division Manager'
        ];
    }
}
