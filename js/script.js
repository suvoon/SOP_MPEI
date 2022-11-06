window.addEventListener("DOMContentLoaded", () => {
    let menu_bar = document.querySelector(".menu-bar");

    //Появление тени у навигационной панели при скролле
    window.addEventListener('scroll', function (e) {
        if (this.window.scrollY > 30) {
            menu_bar.classList.add("bar-scrolled");
        }
        else if (this.window.scrollY < 10) {
            menu_bar.classList.remove("bar-scrolled");
        }
    });

    //Вывод сообщения при неправильном формате файла
    let pfp_change = document.querySelector("#pfp-change");
    let pfp_btn = document.querySelector("#pfp-btn");
    if (pfp_change) {
        pfp_change.addEventListener("change", function () {
            if (this.files[0]['type'].split('/')[0] === 'image') {
                if (pfp_btn.nextSibling.classList.contains("error-message")) {
                    pfp_btn.nextSibling.remove();
                }
            }
            else {
                let file_error = document.createElement("div");
                file_error.classList.add("error-message");
                file_error.style.marginTop = "0.2rem";
                file_error.innerHTML = "Некорректный формат изображения";
                pfp_btn.parentNode.insertBefore(file_error, pfp_btn.nextSibling);
                this.value = '';
            }
        });
    }

    tabcontent = document.querySelectorAll(".tabcontent");
    tablinks = document.querySelectorAll(".tablinks");
    for (let i = 0; i < tablinks.length; i++) {
        tablinks[i].addEventListener("click", function () {
            for (let j = 0; j < tablinks.length; j++) {
                tablinks[j].classList.remove("active");
                tabcontent[j].classList.remove("active");
            }
            tabcontent[i].classList.add("active");
            this.classList.add("active");
        });

    }
    defaultOpen = document.querySelector("#defaultOpen");
    if (defaultOpen) defaultOpen.click();

    editbtns = document.querySelectorAll(".info-block__edit");
    editblock = document.querySelectorAll(".info-block__hidden");
    for (let i = 0; i < editblock.length; i++) {
        editbtns[i].addEventListener("click", function () {
            for (let j = 0; j < editblock.length; j++) {
                editblock[j].classList.remove("active");
            }
            editblock[i].classList.add("active");
        });
        editblock[i].addEventListener("click", function (e) {
            if (e.target !== e.currentTarget) return;
            this.classList.remove("active");
        });
    }

    newpass = document.querySelectorAll(".newpass-btn");
    for (let i = 0; i < newpass.length; i++) {
        newpass[i].addEventListener("click", function () {
            this.parentNode.childNodes[3].style.display = "block";
            this.parentNode.childNodes[4].style.display = "block";
            this.style.display = "none";
        });
    }

    newsurvey = document.querySelector(".survey-constructor__create-btn");
    if (newsurvey) {
        newsurvey.addEventListener("click", function () {
            document.querySelector(".survey-constructor__constructor-block").style.display = "flex";
            this.style.display = "none";
        });
    }
    newblock = document.querySelector(".add-block");
    send_btn = document.querySelector(".send-btn");
    block_cnt = 0;
    blocks = [];
    if (newblock) {
        newblock.addEventListener("click", function () {
            send_btn.classList.remove("hidden");

            tmp_block = document.createElement("div");
            tmp_block.className = "survey-constructor__survey-block";

            tmp_title_block = document.createElement("div");
            tmp_title_block.className = "block-title";
            tmp_label = document.createElement("label");
            tmp_label.innerHTML = "Заголовок (Название дисциплины): ";
            tmp_title = document.createElement("input");
            tmp_title.name = "title[" + block_cnt + "]";
            tmp_title_block.append(tmp_label, tmp_title);

            tmp_add_btn = document.createElement("button");
            with (tmp_add_btn) {
                innerHTML = "Добавить вопрос";
                className = "survey-constructor__create-btn";
                type = "button";
                id = block_cnt;
            }
            tmp_add_btn.addEventListener("click", function () {
                tmp_fset = document.createElement("fieldset");

                tmp_label_r = document.createElement("label");
                tmp_label_r.innerHTML = "Оценка (от 1 до 5): ";
                tmp_label_i = document.createElement("label");
                tmp_label_i.innerHTML = "Обязательное поле: ";
                tmp_label_u = document.createElement("label");
                tmp_label_u.innerHTML = "Необязательное поле: ";

                tmp_input_rate = document.createElement("input");
                with (tmp_input_rate) {
                    type = "radio";
                    name = "question[" + this.id + "][" + blocks[this.id] + "]";
                    value = "rate";
                    checked = true;
                }
                tmp_input_important = document.createElement("input");
                with (tmp_input_important) {
                    type = "radio";
                    name = "question[" + this.id + "][" + blocks[this.id] + "]";
                    value = "important";
                }
                tmp_input_unimportant = document.createElement("input");
                with (tmp_input_unimportant) {
                    type = "radio";
                    name = "question[" + this.id + "][" + blocks[this.id] + "]";
                    value = "unimportant";
                }

                tmp_qtype1 = document.createElement("div");
                tmp_qtype1.className = "question-type";
                tmp_qtype2 = document.createElement("div");
                tmp_qtype2.className = "question-type";
                tmp_qtype3 = document.createElement("div");
                tmp_qtype3.className = "question-type";
                tmp_qfield = document.createElement("textarea");
                with (tmp_qfield) {
                    className = "question-field";
                    name = "qfield[" + this.id + "][" + blocks[this.id] + "]";
                    cols = 30;
                    rows = 4;
                    placeholder = "Введите вопрос";
                }

                tmp_qtype1.append(tmp_label_r, tmp_input_rate);
                tmp_qtype2.append(tmp_label_i, tmp_input_important);
                tmp_qtype3.append(tmp_label_u, tmp_input_unimportant);

                tmp_fset.append(tmp_qtype1, tmp_qtype2, tmp_qtype3, tmp_qfield);
                this.parentNode.appendChild(tmp_fset);
                blocks[this.id]++;
                //fieldset
            });

            tmp_block.append(tmp_title_block, tmp_add_btn);
            send_btn.before(tmp_block);

            blocks[block_cnt] = 0;
            block_cnt++;
        });
    }

});