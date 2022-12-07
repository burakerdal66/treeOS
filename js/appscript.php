<script>
    var canvas = document.getElementById("application");
    var ctx = canvas.getContext("2d");

    var img = new Image();
    img.src = 'images/file_icon.png';
    var folder_icon = new Image();
    folder_icon.src = 'images/folder_icon.png';

    var desktop_context_menu_active = false;
    var selected_context_menu_file = -1;
    var selected_file = -1;
    var opened_folder = "";

	document.addEventListener('mousemove', pointer_stats);
	document.addEventListener('mousedown', switch_drag);
	document.addEventListener('mouseup', switch_drag);

    var pointer = {
        is_dragging: false,
        click_position_x: 0,
        click_position_y: 0,
        pointer_position_x: 0,
        pointer_position_y: 0
    };

    const files = [
        <?php
            foreach($files as $item){
        ?>
        {
            filename: "<?php echo $item['file_name']; ?>",
            position_x: <?php echo $item["position_x"] ?>,
            position_y: <?php echo $item["position_y"] ?>,
            foldername: "<?php echo $item['foldername'] ?>",
            isfolder: <?php echo $item["is_folder"] ?>
        },
        <?php
            }
        ?>
    ];

    refresh();
    function refresh(){
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        if(opened_folder == ""){
            ctx.fillStyle = "#000000";
            ctx.fillRect(0,0,window.innerWidth,window.innerHeight);
            ctx.fillStyle = "#ADF8";
            ctx.font = "28px Arial";
            ctx.fillText("<?php echo $_SESSION['username']; ?>'s desktop", 20, 40);
        } else {
            ctx.fillStyle = "#333";
            ctx.fillRect(0,0,window.innerWidth,window.innerHeight);
            ctx.fillStyle = "#222";
            ctx.fillRect(0,0,window.innerWidth,40);
            if(pointer.pointer_position_x >= window.innerWidth - 100 && pointer.pointer_position_x <= window.innerWidth && pointer.pointer_position_y >= 0 && pointer.pointer_position_y <= 40){
                ctx.fillStyle = "#F44";
            } else {
                ctx.fillStyle = "#F00";
            }
            ctx.fillRect(window.innerWidth - 100,0,100,40);
            ctx.fillStyle = "#FFF";
            ctx.font = "20px Arial";
            ctx.fillText("Close", window.innerWidth - 75, 27);
            ctx.fillStyle = "#ADF";
            ctx.font = "20px Arial";
            ctx.fillText(opened_folder, 35, 27);
            ctx.drawImage(folder_icon, 10, 10, 13, 20)
        }

        if(desktop_context_menu_active){
            ctx.font = "16px Arial";
            if(pointer.pointer_position_x >= pointer.click_position_x && pointer.pointer_position_x <= pointer.click_position_x + 110 && pointer.pointer_position_y >= pointer.click_position_y && pointer.pointer_position_y <= pointer.click_position_y + 30)
                ctx.fillStyle = "#DFFFDF";
            else
                ctx.fillStyle = "#FFFFFF";
            ctx.fillRect(pointer.click_position_x, pointer.click_position_y, 110, 30);
            ctx.fillStyle = "#333333";
            ctx.fillText("Create Folder", pointer.click_position_x + 5, pointer.click_position_y + 20);
            ctx.rect(pointer.click_position_x, pointer.click_position_y, 110, 30);
        }

        files.forEach(item => {
            if(item.foldername == opened_folder){
                if(pointer.pointer_position_x >= item.position_x - 20 && pointer.pointer_position_x <= item.position_x + 90 && pointer.pointer_position_y >= item.position_y - 5 && pointer.pointer_position_y <= item.position_y + 140){   
                    ctx.fillStyle = "#3368";
                    ctx.fillRect(item.position_x - 20, item.position_y - 5, 110, 145)
                }
                if(item.isfolder == 0){
                    if(item.filename.split(".")[1] == "png" || item.filename.split(".")[1] == "jpg"){
                        let fileimage = new Image();
                        fileimage.src = "uploads/" + item.filename;
                        ctx.drawImage(fileimage, item.position_x, item.position_y, 70, 100); 
                    }
                    else{
                        ctx.drawImage(img, item.position_x, item.position_y, 70, 100);
                    }
                    
                    ctx.fillStyle = "#ADF8";
                    ctx.fillRect(item.position_x, item.position_y, 70, 20)
                    ctx.fillStyle = "#111111";
                    ctx.font = "16px Arial";
                    ctx.fillText(item.filename.split(".")[1], item.position_x + 5, item.position_y + 15);
                } else {
                    ctx.drawImage(folder_icon, item.position_x, item.position_y, 70, 100);
                }
                
                ctx.fillStyle = "#FFFFFF";
                ctx.font = "18px Arial";
                if(item.isfolder == 0){
                    ctx.fillText((item.filename.split("_")[2].split(".")[0].length > 6 ? item.filename.split("_")[2].split(".")[0].substring(0,6) + "." : item.filename.split("_")[2].split(".")[0]) + "." + item.filename.split("_")[2].split(".")[1], item.position_x + (item.filename.split("_")[2].split(".")[0].length >= 6 ? - 15 : + 0), item.position_y + 130);
                } else {
                    ctx.fillText((item.filename.length > 10 ? item.filename.substring(0,10) + "..." : item.filename), item.position_x + (item.filename.length >= 10 ? - 15 : + 0), item.position_y + 130);
                }
            }
        });
        if(selected_context_menu_file != -1){
            ctx.font = "16px Arial";

            if(pointer.pointer_position_x >= pointer.click_position_x && pointer.pointer_position_x <= pointer.click_position_x + 100 && pointer.pointer_position_y >= pointer.click_position_y && pointer.pointer_position_y <= pointer.click_position_y + 30)
                ctx.fillStyle = "#DFFFDF";
            else
                ctx.fillStyle = "#FFFFFF";
            ctx.fillRect(pointer.click_position_x, pointer.click_position_y, 100, 30);
            ctx.fillStyle = "#333333";
            ctx.fillText("Download", pointer.click_position_x + 5, pointer.click_position_y + 20);
            ctx.rect(pointer.click_position_x, pointer.click_position_y, 100, 30);

            if(pointer.pointer_position_x >= pointer.click_position_x && pointer.pointer_position_x <= pointer.click_position_x + 100 && pointer.pointer_position_y >= pointer.click_position_y + 30 && pointer.pointer_position_y <= pointer.click_position_y + 60)  
                ctx.fillStyle = "#DFFFDF";
            else
                ctx.fillStyle = "#FFFFFF";
            ctx.fillRect(pointer.click_position_x, pointer.click_position_y + 30, 100, 30);
            ctx.fillStyle = "#333333";
            ctx.fillText("Rename", pointer.click_position_x + 5, pointer.click_position_y + 50);
            ctx.rect(pointer.click_position_x, pointer.click_position_y + 30, 100, 30);

            if(pointer.pointer_position_x >= pointer.click_position_x && pointer.pointer_position_x <= pointer.click_position_x + 100 && pointer.pointer_position_y >= pointer.click_position_y + 60 && pointer.pointer_position_y <= pointer.click_position_y + 90)  
                ctx.fillStyle = "#FFDFDF";
            else
                ctx.fillStyle = "#FFFFFF";
            ctx.fillRect(pointer.click_position_x, pointer.click_position_y + 60, 100, 30);
            ctx.fillStyle = "#333333";
            ctx.fillText("Delete", pointer.click_position_x + 5, pointer.click_position_y + 80);
            ctx.rect(pointer.click_position_x, pointer.click_position_y + 60, 100, 30);

            ctx.stroke();
        }
    }
        
    function pointer_stats(e){
        pointer.pointer_position_x = e.clientX;
        pointer.pointer_position_y = e.clientY;

        if(pointer.is_dragging){
            if(selected_file != -1){
                files[selected_file].position_x = pointer.pointer_position_x - (75 / 2);
                files[selected_file].position_y = pointer.pointer_position_y - (100 / 2);
            }
        }

        refresh();
    }
    function close_center_div(){
        document.getElementById("body").innerHTML = "";
    }
    function switch_drag(){
        if(opened_folder != ""){
            if(pointer.pointer_position_x >= window.innerWidth - 100 && pointer.pointer_position_x <= window.innerWidth && pointer.pointer_position_y >= 0 && pointer.pointer_position_y <= 40){
                opened_folder = "";
            }
        } else {
            if(desktop_context_menu_active){
                if(pointer.pointer_position_x >= pointer.click_position_x && pointer.pointer_position_x <= pointer.click_position_x + 110 && pointer.pointer_position_y >= pointer.click_position_y && pointer.pointer_position_y <= pointer.click_position_y + 30){
                    document.getElementById("body").innerHTML = "<div class='center-div'><h3>Create Folder</h3><hr><form action='create_folder.php' method='POST'><input type='hidden' name='position_x' value='" + pointer.click_position_x + "'><input type='hidden' name='position_y' value='" + pointer.click_position_y + "'><table><tr><td>Folder Name</td><td><input type='text' name = 'foldername'></td></tr><tr><td colspan='2'><input style='width:100%;padding:5px;' type='submit' value='Create'></td></tr><tr><td colspan='2'><input style='width:100%;padding:5px;' type='button' onclick='close_center_div()' value='Close'></td></tr></table></form></div>";
                }
                desktop_context_menu_active = false;
            }
        }
        if(selected_context_menu_file != -1){
            if(pointer.pointer_position_x >= pointer.click_position_x && pointer.pointer_position_x <= pointer.click_position_x + 100 && pointer.pointer_position_y >= pointer.click_position_y && pointer.pointer_position_y <= pointer.click_position_y + 30){
                if(files[selected_context_menu_file].isfolder == 0){
                    document.getElementById("body").innerHTML = "<a href='uploads/" + files[selected_context_menu_file].filename + "' download id='download-file-link' style='display:none;'>";
                    document.getElementById("download-file-link").click();
                    document.getElementById("body").innerHTML = "";
                }
            }
            if(pointer.pointer_position_x >= pointer.click_position_x && pointer.pointer_position_x <= pointer.click_position_x + 100 && pointer.pointer_position_y >= pointer.click_position_y + 30 && pointer.pointer_position_y <= pointer.click_position_y + 60){
                document.getElementById("body").innerHTML = "<div class='center-div'><h3>Rename File</h3><hr><form action='rename_file.php' method='POST'><table><tr><td><input name='filename' type='hidden' value = '" + files[selected_context_menu_file].filename + "'></td></tr><tr><td>From</td><td>" + files[selected_context_menu_file].filename.split("_")[2] + "</td></tr><tr><td>To</td><td><input type='text' name = 'tofilename' value = '" + files[selected_context_menu_file].filename.split("_")[2] + "'></td></tr><tr><td colspan='2'><input style='width:100%;padding:5px;' type='submit' value='Rename'></td></tr><tr><td colspan='2'><input style='width:100%;padding:5px;' type='button' onclick='close_center_div()' value='Close'></td></tr></table></form></div>";
            }
            if(pointer.pointer_position_x >= pointer.click_position_x && pointer.pointer_position_x <= pointer.click_position_x + 100 && pointer.pointer_position_y >= pointer.click_position_y + 60 && pointer.pointer_position_y <= pointer.click_position_y + 90){
                var form_data = new FormData();                  
                form_data.append('filename', files[selected_context_menu_file].filename);

                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "delete_file.php", true);
                xhttp.onload = function(event) {
                    window.location.href = "desktop.php";
                }

                xhttp.send(form_data);
            }
            
            selected_context_menu_file = -1;
            refresh();
        }
        pointer.is_dragging = !pointer.is_dragging;

        pointer.click_position_x = pointer.pointer_position_x;
        pointer.click_position_y = pointer.pointer_position_y;

        for(let i = 0; i < files.length; i++){
            if(pointer.click_position_x >= files[i].position_x - 20 && pointer.click_position_x <= files[i].position_x + 90){
                if(pointer.click_position_y >= files[i].position_y - 5 && pointer.click_position_y <= files[i].position_y + 140){
                    if(pointer.is_dragging && selected_file == -1){
                        if(files[i].foldername == opened_folder){
                            selected_file = i;
                            break;
                        }
                    }
                }
            }
        }
        
        if(!pointer.is_dragging){
            if(selected_file != -1){
                for(let i = 0; i < files.length; i++){
                    if(pointer.click_position_x >= files[i].position_x - 20 && pointer.click_position_x <= files[i].position_x + 90){
                        if(pointer.click_position_y >= files[i].position_y - 5 && pointer.click_position_y <= files[i].position_y + 140){
                            if(files[i].isfolder == 1 && i != selected_file){
                                
                                var form_data = new FormData();
                                form_data.append('foldername', files[i].filename);
                                form_data.append('filename', files[selected_file].filename);

                                var xhttp = new XMLHttpRequest();
                                xhttp.open("POST", "add_to_folder.php", true);
                                xhttp.onload = function(event) {
                                    window.location.href = "desktop.php";
                                }
                        
                                xhttp.send(form_data);
                                break;
                            }
                        }
                    }
                }
                save_desktop_fn();
            }
            selected_file = -1;
        }
        
        refresh();
    }
    
    function drop(e){
        e.preventDefault();
        file_obj = e.dataTransfer.files[0];

        var form_data = new FormData();
        form_data.append('file', file_obj);
        form_data.append('position_x', e.clientX);
        form_data.append('position_y', e.clientY);

        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "file_upload.php", true);
        xhttp.onload = function(event) {
            window.location.href = "desktop.php";
        }
 
        xhttp.send(form_data);
    }
    function allow_drop(e){
        e.preventDefault();
    }

    function save_desktop_fn(){
        files.forEach(item => {
            var form_data = new FormData();
            form_data.append('filename', item.filename);
            form_data.append('position_x', item.position_x);
            form_data.append('position_y', item.position_y);

            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "save_desktop.php", true);
            xhttp.send(form_data);
        });
    }

    function open_file(){
        for(let i = 0; i < files.length; i++){
            if(pointer.click_position_x >= files[i].position_x - 20 && pointer.click_position_x <= files[i].position_x + 90){
                if(pointer.click_position_y >= files[i].position_y - 5 && pointer.click_position_y <= files[i].position_y + 140){
                    if(files[i].isfolder == 0){
                        window.open("uploads/"+files[i].filename, "_blank");
                    } else {
                        opened_folder = files[i].filename;
                    }
                    break;
                }
            }
        }
    }

    window.addEventListener('contextmenu', (event) => {
        event.preventDefault();

        for(let i = 0; i < files.length; i++){
            if(opened_folder == files[i].foldername){
                if(pointer.click_position_x >= files[i].position_x - 20 && pointer.click_position_x <= files[i].position_x + 90){
                    if(pointer.click_position_y >= files[i].position_y - 5 && pointer.click_position_y <= files[i].position_y + 140){
                        selected_context_menu_file = i;
                        break;
                    }
                }
            }
        }
        if(selected_context_menu_file == -1){
            if(opened_folder == ""){
                desktop_context_menu_active = true;
            }
        }
        
        refresh();
    });
</script>
