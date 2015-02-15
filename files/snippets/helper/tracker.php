<?php
require_once 'common.php';
require_once 'mssql.php';

/*
 helper_tracker
 DealerProfile Tracker helpers
 mReschke 2012-11-09
*/
class helper_tracker {
	public $server;
	public $db;
	public $trackerNum;
	public $trackerCopy;
	public $submitterID;
	public $ownerID;
	public $assignedID;
	public $submitDate;
	public $closeDate;
	public $statusID;
	public $priorityID;
	public $reasonID;
	public $departmentID;
	public $projectID;
	public $groupID;
	public $distributorID;
	public $dealerID;
	public $summary;
	public $description;
	public $statusDateTime;
	public $hours;
	public $startDate;
	public $endDate;
	public $note;
	public $noteOwnerID;

	public function __construct() {
		$this->server = 'dyna-sql2';
		$this->db = 'DealerProfile';
	}

	public function insert_tracker(dealerprofile_tracker $tracker) {
		$sql = new helper_mssql($this->server, $this->db);
		$tracker->description = preg_replace("/'/", "''", $tracker->description);
		$tracker->summary = preg_replace("/'/", "''", $tracker->summary);
		#$tracker->summary = preg_replace("/'/", "''", $tracker->summary);
		$tracker->note = preg_replace("/'/", "''", $tracker->note);

		
		$sql->query = "
			DECLARE @newTrackerNum as int
			SET @newTrackerNum = (SELECT MAX(trackerNum)+1 FROM dbo.tblTrkTrackers)

			INSERT INTO dbo.tblTrkTrackers (
				trackerNum, trackerCopy, submitterID, ownerID, assignedID, submitDate, closeDate, statusID, priorityID, severityID,
				reasonID, departmentID, projectID, groupID, distributorID, dealerID, summary, description, statusDateTime, hours, startDate, endDate
			) VALUES (
				@newTrackerNum,
				".$sql->isnull($tracker->trackerCopy, 'null').",
				".$sql->isnull($tracker->submitterID, $this->get_user_id('mreschke')).",
				".$sql->isnull($tracker->ownerID, -1).",
				".$sql->isnull($tracker->assignedID, -1).",
				'".date("Y-m-d H:i:s")."',
				".($tracker->statusID == $this->get_status_id('closed') ? "'".date("Y-m-d H:i:s")."'" : "null").",
				".$sql->isnull($tracker->statusID, $this->get_status_id('open')).",
				".$sql->isnull($tracker->priorityID, -1).",
				-1,
				".$sql->isnull($tracker->reasonID, -1).",
				".$sql->isnull($tracker->departmentID, -1).",
				".$sql->isnull($tracker->projectID, -1).",
				".$sql->isnull($tracker->groupID, -1).",
				".$sql->isnull($tracker->distributorID, -1).",
				".$sql->isnull($tracker->dealerID, -1).",
				'".$sql->isnull($tracker->summary, 'Empty Summary', false)."',
				'".$sql->isnull($tracker->description, 'Empty Description', false)."',
				".$sql->isnull($tracker->statusDateTime, 'null').",
				".$sql->isnull($tracker->hours, 'null').",
				".$sql->isnull($tracker->startDate, 'null').",
				".$sql->isnull($tracker->endDate, 'null')."
			) ";

		if (isset($tracker->note)) {
			$sql->query .= "
				INSERT INTO dbo.tblTrkNotes (
					trackerID, noteOwnerID, noteDateTime, note, history
				) VALUES (
					@@IDENTITY,
					".$sql->isnull($tracker->noteOwnerID, $tracker->submitterID).",
					'".date("Y-m-d H:i:s")."',
					'".$tracker->note."',
					0
				)
			";
		}
			
		$sql->query .= "SELECT @newTrackerNum";
		
		#echo "<pre>".$sql->query."</pre>";
		$sql->execute();
		$this->trackerNum = $sql->get_scalar();
		$sql->disconnect();
		return $this->trackerNum;
	}

	public function get_user_id($username) {
		$sql = new helper_mssql($this->server, $this->db);
		if (preg_match("'@'", $username)) {
			$sql->query = "SELECT TOP 1 l.ID FROM tblLogin l INNER JOIN tblContacts c on l.contactID = c.ID WHERE email = '$username'";
		} else {
			$sql->query = "SELECT TOP 1 ID FROM dbo.tblLogin WHERE username = '$username'";	
		}
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_email_from_id($userID) {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT c.email FROM tblLogin l INNER JOIN tblContacts c on l.contactID = c.ID WHERE l.ID = $userID";
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_status_id($status) {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT TOP 1 ID FROM dbo.tblTrkStatuses WHERE status LIKE '%$status%'";
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_priority_id($priority) {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT TOP 1 ID FROM dbo.tblTrkPriorities WHERE priority LIKE '%$priority%'";
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_reason_id($reason) {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT TOP 1 ID FROM dbo.tblTrkReasons WHERE reason LIKE '%$reason%'";
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_department_id($department) {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT TOP 1 ID FROM dbo.tblTrkDepartments WHERE department LIKE '%$department%'";
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_project_id($project) {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT TOP 1 productID AS ID FROM dbo.tblProducts WHERE product LIKE '%$project%'";
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_group_id($group) {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT TOP 1 ID FROM dbo.tblGroups WHERE DealerGroup LIKE '%$group%'";
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_distributor_id($distributor) {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT TOP 1 ID FROM dbo.tblDistributors WHERE Distributor LIKE '%$distributor%'";
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_dealer_id($dealer) {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT TOP 1 ID FROM dbo.tblDealers WHERE DealerName LIKE '%$dealer%'";
		$sql->execute();
		return $sql->get_scalar();
	}

	public function get_statuses() {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT * FROM dbo.tblTrkStatuses";
		$sql->execute();
		$statuses = array('');
		for ($r = 0; $r <= $sql->row_count-1; $r++) {
			$row = mssql_fetch_array($sql->result);
			$statuses[] = $row['status'];
		}
		return $statuses;
	}

	public function get_priorities() {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT * FROM dbo.tblTrkPriorities";
		$sql->execute();
		$statuses = array('');
		for ($r = 0; $r <= $sql->row_count-1; $r++) {
			$row = mssql_fetch_array($sql->result);
			$statuses[] = $row['priority'];
		}
		return $statuses;
	}

	public function get_reasons() {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT * FROM dbo.tblTrkReasons";
		$sql->execute();
		$statuses = array('');
		for ($r = 0; $r <= $sql->row_count-1; $r++) {
			$row = mssql_fetch_array($sql->result);
			$statuses[] = $row['reason'];
		}
		return $statuses;
	}

	public function get_departments() {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT * FROM dbo.tblTrkDepartments";
		$sql->execute();
		$statuses = array('');
		for ($r = 0; $r <= $sql->row_count-1; $r++) {
			$row = mssql_fetch_array($sql->result);
			$statuses[] = $row['department'];
		}
		return $statuses;
	}

	public function get_projects() {
		$sql = new helper_mssql($this->server, $this->db);
		$sql->query = "SELECT * FROM dbo.tblProducts";
		$sql->execute();
		$statuses = array('');
		for ($r = 0; $r <= $sql->row_count-1; $r++) {
			$row = mssql_fetch_array($sql->result);
			$statuses[] = $row['product'];
		}
		return $statuses;
	}	

}

class helper_tracker_form extends helper_tracker {
	public $users;
	public $submitter;
	public $owner;
	public $assigned;
	public $status;
	public $priority;
	public $reason;
	public $department;
	public $project;
	#public $groupID;
	#public $distributorID;
	#public $dealerID;
	#public $statusDateTime;
	#public $hours;
	#public $startDate;
	#public $endDate;
	#public $note;
	#public $noteOwnerID;

	public function show_form() {
		require_once 'form.php';

		$form = new helper_form;

		if ($form->post->btnCreateTracker) {
			require_once 'email.php';

			$this->submitterID = $this->get_user_id($form->post->selSubmitter);
			$this->ownerID = $this->get_user_id($form->post->selOwner);
			$this->assignedID = $this->get_user_id($form->post->selAssigned);
			$this->statusID = $this->get_status_id($form->post->selStatus);
			$this->priorityID = $this->get_priority_id($form->post->selPriority);
			$this->reasonID = $this->get_reason_id($form->post->selReason);
			$this->departmentID = $this->get_department_id($form->post->selDepartment);
			$this->projectID = $this->get_project_id($form->post->selProject);
			$this->summary = preg_replace("'\r\n'", "<br />", $form->post->txtSummary);
			$this->description = preg_replace("'\r\n'", "<br />", $form->post->txtDescription);
			$emailto = implode(", ", $form->post->selEmail);
			if (count($emailto) == 0) $emailto = $this->get_email_from_id($this->assignedID);


			$this->note = preg_replace("'\r\n'", "<br />", $form->post->txtNote);
			$this->note = "<font color='cadetblue'><b>Emailed via Wiki To:</b></font><br />".$emailto."<br /><br /><font color='cadetblue'><b>Email Message:</b></font><br />".$this->note;

			$trackerNum = $this->insert_tracker($this);
			echo "<font color='red'>Tracker #$trackerNum Created!</font><br />";


			$email = new helper_email(preg_replace("' '", "", $emailto));
			$email->from = $this->get_email_from_id($this->submitterID);
			$email->subject = $trackerNum.' '.$form->post->selReason.': '.strip_tags($form->post->txtSummary);
			$email->body = "<b>".$form->post->selSubmitter." has sent you DynaTracker <a href='http://www.dealerprofiler.com/trackerInfo.aspx?trackerNum=".$trackerNum."'>#".$trackerNum."</a></b>";
			$email->body .= "<p>".$this->summary.'</p><hr /><p>'.$this->description."</p><hr />";
			$email->body .= "<p>".$this->note."</p>";
			$email->send();

			$this->summary = "";
			$this->description = "";
			$this->note = "";
		
		}
		sort($this->users);
		$statuses = $this->get_statuses(); if (!$this->status) $this->status = 'Open';
		$priorities = $this->get_priorities();
		$reasons = $this->get_reasons();
		$departments = $this->get_departments();
		$projects = $this->get_projects();
		
		echo "<style>
			.trackerform th {
				text-align: right;
			}
			.trackerform th, .trackerform td {
				padding-bottom: 5px;
			}
		</style>";
		echo "<table border='0' class='trackerform'>";
			echo "<tr>";
				echo "<th>Department:</th>";
				echo "<td>";
					echo "<select name='selDepartment'>";
						foreach ($departments as $department) {
							echo "<option ".(strtolower($department) == strtolower($this->department) ? "selected='selected'" : '').">$department</option>";
						}
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<th>Project:</th>";
				echo "<td>";
					echo "<select name='selProject'>";
						foreach ($projects as $project) {
							echo "<option ".(strtolower($project) == strtolower($this->project) ? "selected='selected'" : '').">$project</option>";
						}
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<th>Reason:</th>";
				echo "<td>";
					echo "<select name='selReason'>";
						foreach ($reasons as $reason) {
							echo "<option ".(strtolower($reason) == strtolower($this->reason) ? "selected='selected'" : '').">$reason</option>";
						}
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<th>Priority:</th>";
				echo "<td>";
					echo "<select name='selPriority'>";
						foreach ($priorities as $priority) {
							echo "<option ".(strtolower($priority) == strtolower($this->priority) ? "selected='selected'" : '').">$priority</option>";
						}
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<th>Status:</th>";
				echo "<td>";
					echo "<select name='selStatus'>";
						foreach ($statuses as $status) {
							echo "<option ".(strtolower($status) == strtolower($this->status) ? "selected='selected'" : '').">$status</option>";
						}
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<th>Submitter:</th>";
				echo "<td>";
					echo "<select name='selSubmitter'>";
						foreach ($this->users as $user) {
							echo "<option ".(strtolower($user) == strtolower($this->submitter) ? "selected='selected'" : '').">$user</option>";
						}
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<th>Owner:</th>";
				echo "<td>";
					echo "<select name='selOwner'>";
						foreach ($this->users as $user) {
							echo "<option ".(strtolower($user) == strtolower($this->owner) ? "selected='selected'" : '').">$user</option>";
						}
					echo "</select>";
				echo "</td>";
			echo "<tr>";
				echo "<th>Assigned:</th>";
				echo "<td>";
					echo "<select name='selAssigned'>";
						foreach ($this->users as $user) {
							echo "<option ".(strtolower($user) == strtolower($this->assigned) ? "selected='selected'" : '').">$user</option>";
						}
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<th>Email To:</th>";
				echo "<td>";
					echo "<select name='selEmail[]' multiple style='height:82px'>";
						foreach ($this->users as $user) {
							echo "<option>$user</option>";
						}
					echo "</select>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><b>Summary</b></td>";
				echo "<td>&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan='2'><textarea style='width:97%;height:50px' name='txtSummary'>".$this->summary."</textarea></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><b>Description</b></td>";
				echo "<td>&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan='2'><textarea style='width:97%;height:100px' name='txtDescription'>".$this->description."</textarea></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><b>Note<b></td>";
				echo "<td>&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan='2'><textarea style='width:97%;height:150px' name='txtNote'>".$this->note."</textarea></td>";
			echo "</tr>";
		echo "</table>";

		echo $form->submit('btnCreateTracker', 'Create & Email Tracker');
	}
}