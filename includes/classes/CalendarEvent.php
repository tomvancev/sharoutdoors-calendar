<?php
class CalendarEvent {
  public $id;
  public $post_id;
  public $dateFrom;
  public $dateTo;
  private $db;



    public function __construct($id, $dateFrom, $dateTo, $post_id) {
      $this->db = new CalendarDatabase();
      if (isset($id)) {
        $this->load($id);
      } else {
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->post_id  = $post_id;
      }
    }

    private function load($id){
      $result = $this->db->get_by_id($id);

      $this->id       = $result['id'];
      $this->dateFrom = $result['date_from'];
      $this->dateTo   = $result['date_to'];
      $this->post_id  = $result['post_id'];
    }

    public function create(){
      return $this->db->insert($this->dateFrom,$this->dateTo, $this->post_id );
    }

    public function delete(){
      $idDeleted = $this->db->delete_by_id();
      return !empty($idDeleted) ? $isDeleted: new WP_ERROR("Error deleting the event");
    }

    public function to_JSON(){
      return json_encode( array(
        'id'      => $this->id,
        'date_from' => $this->dateFrom,
        'date_to' => $this->dateTo,
        'post_id' => $this->post_id

      ) );

    }

// end of class

}
