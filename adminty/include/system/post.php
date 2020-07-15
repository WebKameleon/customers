<?php
    if ($this->webtd['page_id']<0 && $this->mode==2)
        return;
    
    if ($this->mode<=1)
        return;


?>
<div class="form-group row">
    <div class="col-12">
        <div class="checkbox-fade fade-in-primary d-">
            <label>
                <input type="hidden" name="loopback[auth]" value="0"/>
                <input type="checkbox" name="loopback[auth]" value="1" <?php if($loopback['auth']) echo 'checked';?>>
                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                <span class="text-inverse">Add auth header</span>
            </label>
        </div>
    </div>
</div>
<div class="row m-t-30">
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20">Save module</button>
        
    </div>
</div>
</form>
