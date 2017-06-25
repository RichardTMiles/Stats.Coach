

<?php if (!isset($this->alert)) return;

function PHP2JS($message, $level)
{
    echo "<script>bootstrapAlert('$message', '$level')</script>";
}

if (isset($this->alert['danger'])) PHP2JS( $this->alert['danger'], 'danger' );
if (isset($this->alert['info']))   PHP2JS( $this->alert['info'], 'info' );
if (isset($this->alert['warning'])) PHP2JS( $this->alert['warning'], 'warning' );
if (isset($this->alert['success'])) PHP2JS( $this->alert['success'], 'success' );

?>
