import{a as E,i as x}from"./codemirror-editor-3972d91d.js";function k(e){const c=document.createElement("div");c.className="assignment-create__page",c.setAttribute("data-page-index",e),c.setAttribute("data-page-type","text");const g=`tinymce-editor-page-${e}`;return c.innerHTML=`
        <div class="assignment-create__page-header">
            <h3 class="assignment-create__page-title">Страница ${e+1}</h3>
            <button type="button" class="assignment-create__page-remove" data-page-index="${e}">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="assignment-create__page-body">
            <div class="assignment-create__form-group form-group">
                <label for="page_title_${e}" class="assignment-create__label">Заголовок страницы</label>
                <input type="text" id="page_title_${e}" name="pages[${e}][title]" class="assignment-create__input" required>
            </div>
            <div class="assignment-create__form-group form-group">
                <label for="${g}" class="assignment-create__label">Содержимое страницы</label>
                <textarea id="${g}" name="pages[${e}][content]" class="assignment-create__textarea tinymce-editor" rows="10"></textarea>
            </div>
        </div>
    `,c}function A(e){console.log("Creating code page with index:",e);const c=`
        <div class="assignment-create__page" data-page-index="${e}" data-page-type="code">
            <div class="assignment-create__page-header">
                <h3 class="assignment-create__page-title">Страница ${e+1}</h3>
                <button type="button" class="assignment-create__page-remove" data-page-index="${e}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="assignment-create__page-body">
                <div class="assignment-create__form-group form-group">
                    <label for="page_title_${e}" class="assignment-create__label">Заголовок страницы</label>
                    <input type="text" id="page_title_${e}" name="pages[${e}][title]" class="assignment-create__input" required>
                </div>
                <div class="assignment-create__form-group form-group">
                    <label for="page_description_${e}" class="assignment-create__label">Описание задания</label>
                    <textarea id="page_description_${e}" name="pages[${e}][description]" class="assignment-create__textarea" rows="3"></textarea>
                </div>
                <div class="assignment-create__form-group assignment-create__edit-code">
                    <div class="assignment-create__form-group assignment-create__theme-code">
                        <label class="assignment-create__label">Настройки редактора</label>
                        <div class="assignment-create__code-toolbar">
                            <select class="theme-selector">
                                <option value="default">Светлая тема</option>
                                <option value="dracula">Dracula</option>
                                <option value="monokai">Monokai</option>
                                <option value="material">Material</option>
                            </select>
                        </div>
                    </div>
                    <div class="assignment-create__code-toolbar">
                        <button type="button" class="download-zip">
                            <i class="fas fa-download"></i> Скачать как архив
                        </button>
                    </div>
                </div>
                <div class="editor-three-panel">
                    <div class="form-group__code-wrapper">
                        <div class="assignment-create__code-wrapper">
                            <div class="assignment-create__form-group form-group">
                                <label class="assignment-create__label">HTML</label>
                                <div class="html-editor"></div>
                                <textarea name="pages[${e}][html]" style="display: none;"></textarea>
                            </div>
                            <div class="assignment-create__form-group form-group">
                                <label class="assignment-create__label">CSS</label>
                                <div class="css-editor"></div>
                                <textarea name="pages[${e}][css]" style="display: none;"></textarea>
                            </div>
                        </div>
                        <div class="preview-panel">
                            <div class="assignment-create__form-group form-group">
                                <label class="assignment-create__label">Предпросмотр</label>
                                <iframe class="preview-frame" style="height: 300px; border: 1px solid #ddd;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,g=document.createElement("div");g.innerHTML=c;const f=g.firstElementChild;return console.log("Created page element:",f),Promise.resolve(f)}async function L(e){console.log("Creating test page with index:",e);const c=`
        <div class="assignment-create__page test" data-page-index="${e}" data-page-type="test">
            <div class="assignment-create__page-header">
                <h3 class="assignment-create__page-title">Страница ${e+1}</h3>
                <button type="button" class="assignment-create__page-remove" data-page-index="${e}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="assignment-create__page-body">
                <div class="assignment-create__form-group form-group">
                    <label for="page_title_${e}" class="assignment-create__label">Заголовок страницы</label>
                    <input type="text" id="page_title_${e}" name="pages[${e}][title]" class="assignment-create__input" required>
                </div>
                <div class="assignment-create__form-group form-group">
                    <label for="page_description_${e}" class="assignment-create__label">Описание задания</label>
                    <textarea id="page_description_${e}" name="pages[${e}][description]" class="assignment-create__textarea" rows="3"></textarea>
                </div>
                <div class="assignment-create__form-group form-group">
                    <button type="button" class="add-question btn">
                        <i class="fas fa-plus"></i> Добавить вопрос
                    </button>
                </div>
                <div class="test-questions__container" style="display: none;">
                    <!-- Здесь будут добавляться вопросы -->
                </div>
            </div>
        </div>
    `,g=document.createElement("div");g.innerHTML=c;const f=g.firstElementChild,m=f.querySelector(".add-question"),l=f.querySelector(".test-questions__container");m||console.error("Add question button not found"),l||console.error("Questions container not found");function p(){if(!l)return;l.querySelectorAll(".test-question").length===0?l.style.display="none":l.style.display="block"}return p(),m&&l&&m.addEventListener("click",async()=>{const y=l.children.length,o=await D(e,y);l.appendChild(o),p()}),console.log("Created test page element:",f),f}async function D(e,c){console.log("Creating question with pageIndex:",e,"questionIndex:",c);const g=`
        <div class="test-question" data-question-index="${c}">
            <div class="test-question__header">
                <h4>Вопрос ${c+1}</h4>
                <button type="button" class="remove-question btn btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="test-question__body">
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">Текст вопроса</label>
                    <textarea name="pages[${e}][questions][${c}][text]" class="assignment-create__textarea" rows="3" required></textarea>
                </div>
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">Тип вопроса</label>
                    <select class="question-type" name="pages[${e}][questions][${c}][type]">
                        <option value="single">Один правильный ответ</option>
                        <option value="multiple">Несколько правильных ответов</option>
                        <option value="text">Текстовый ответ</option>
                    </select>
                </div>
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">Изображение к вопросу (опционально)</label>
                    <input type="file" class="question-image" accept="image/*">
                    <div class="question-image-preview" style="display: none;">
                        <img src="" alt="Preview" style="max-width: 200px; max-height: 200px;">
                    </div>
                </div>
                <div class="assignment-create__form-group form-group">
                    <button type="button" class="add-answer btn">
                        <i class="fas fa-plus"></i> Добавить ответ
                    </button>
                </div>
                <div class="test-answers__container" style="display: none;">
                    <!-- Здесь будут добавляться ответы -->
                </div>
                <div class="text-answer-container" style="display: none;">
                    <div class="assignment-create__form-group form-group">
                        <label class="assignment-create__label">Правильный ответ</label>
                        <textarea name="pages[${e}][questions][${c}][correct_answer]" class="assignment-create__textarea" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
    `,f=document.createElement("div");f.innerHTML=g;const m=f.firstElementChild,l=m.querySelector(".add-answer"),p=m.querySelector(".test-answers__container"),y=m.querySelector(".question-type"),o=m.querySelector(".remove-question"),s=m.querySelector(".question-image"),n=m.querySelector(".question-image-preview"),u=m.querySelector(".text-answer-container");function r(){if(!p)return;p.querySelectorAll(".test-answer").length===0?p.style.display="none":p.style.display="block"}return r(),y&&y.addEventListener("change",i=>{const t=i.target.value,a=p.querySelectorAll(".test-answer");t==="text"?(p&&(p.style.display="none"),l&&(l.style.display="none"),u&&(u.style.display="block")):(u&&(u.style.display="none"),l&&(l.style.display="block"),r(),a.forEach(_=>{const d=_.querySelector(".answer-correct");d&&(t==="multiple"?(d.type="checkbox",d.name=`pages[${e}][questions][${c}][correct_answers][]`):(d.type="radio",d.name=`pages[${e}][questions][${c}][correct_answer]`))}))}),s&&n&&s.addEventListener("change",i=>{const t=i.target.files[0];if(t){const a=new FileReader;a.onload=_=>{const d=n.querySelector("img");d&&(d.src=_.target.result,n.style.display="block")},a.readAsDataURL(t)}}),l&&p&&l.addEventListener("click",async()=>{const i=p.children.length,t=await M(e,c,i),a=y.value,_=t.querySelector(".answer-correct");_&&(a==="multiple"?(_.type="checkbox",_.name=`pages[${e}][questions][${c}][correct_answers][]`):(_.type="radio",_.name=`pages[${e}][questions][${c}][correct_answer]`)),p.appendChild(t),r()}),o&&o.addEventListener("click",()=>{m.remove();const i=m.closest(".test-questions__container");i&&i.querySelectorAll(".test-question").length===0&&(i.style.display="none")}),console.log("Created question element:",m),m}async function M(e,c,g){console.log("Creating answer with pageIndex:",e,"questionIndex:",c,"answerIndex:",g);const f=`
        <div class="test-answer" data-answer-index="${g}">
            <div class="test-answer__header">
                <h5>Ответ ${g+1}</h5>
                <button type="button" class="remove-answer btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="test-answer__body">
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">Текст ответа</label>
                    <input type="text" name="pages[${e}][questions][${c}][answers][${g}][text]" class="assignment-create__input" required>
                </div>
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">
                        <input type="radio" class="answer-correct" name="pages[${e}][questions][${c}][correct_answer]" value="${g}">
                        Правильный ответ
                    </label>
                </div>
            </div>
        </div>
    `,m=document.createElement("div");m.innerHTML=f;const l=m.firstElementChild,p=l.querySelector(".remove-answer");return p&&p.addEventListener("click",()=>{l.remove();const y=l.closest(".test-answers__container");y&&y.querySelectorAll(".test-answer").length===0&&(y.style.display="none")}),console.log("Created answer element:",l),l}document.addEventListener("DOMContentLoaded",function(){console.log("Assignment Modal JS loaded"),E();const e=document.querySelector(".assignment-create__pages");if(!e){console.error("Container for pages not found");return}document.querySelectorAll(".assignment-create__page-remove").forEach(o=>{o.addEventListener("click",()=>{const s=o.closest(".assignment-create__page");s&&(s.remove(),m())})});function g(o){console.log("Creating page of type:",o);const s=document.querySelector(".assignment-create__pages");if(!s){console.error("Container for pages not found");return}const n=s.children.length;let u;switch(o){case"text":u=k(n),f(u,n,o);break;case"code":A(n).then(r=>{f(r,n,o)}).catch(r=>{console.error("Error creating code page:",r)});break;case"test":L(n).then(r=>{f(r,n,o)}).catch(r=>{console.error("Error creating test page:",r)});break;default:console.error("Unknown page type:",o);return}}function f(o,s,n){const u=document.querySelector(".assignment-create__pages");if(!u){console.error("Container for pages not found");return}const r=document.createElement("div");r.style.display="none";const i=document.createElement("input");i.type="hidden",i.name=`pages[${s}][type]`,i.value=n,r.appendChild(i);const t=document.createElement("div");t.className="page-content",t.setAttribute("data-page-index",s),t.setAttribute("data-page-type",n);const a=document.createElement("div");a.className="assignment-create__page",a.setAttribute("data-page-index",s),a.appendChild(r),a.appendChild(t),a.appendChild(o),u.appendChild(a),n==="code"?setTimeout(()=>{x(a)},100):n==="text"&&setTimeout(()=>{const d=o.querySelector(".tinymce-editor");d&&!d.id&&(d.id="tinymce-"+Math.random().toString(36).substr(2,9)),d&&d.id&&E("#"+d.id)},100);const _=a.querySelector(".assignment-create__page-remove");_&&_.addEventListener("click",()=>{a.remove(),m()})}function m(){document.querySelectorAll(".assignment-create__page").forEach((s,n)=>{s.setAttribute("data-page-index",n);const u=s.querySelector('input[name*="[type]"]');u&&(u.name=`pages[${n}][type]`);const r=s.querySelector('input[name*="[title]"]');r&&(r.name=`pages[${n}][title]`);const i=s.querySelector('textarea[name*="[description]"]');i&&(i.name=`pages[${n}][description]`);const t=s.querySelector(".assignment-create__page-remove");t&&t.setAttribute("data-page-index",n);const a=s.querySelector(".page-content");a&&a.setAttribute("data-page-index",n)})}const l=document.querySelector(".assignment-create__add-page");l&&l.addEventListener("click",()=>{const o=document.querySelector(".page-type-buttons");o&&o.remove();const s=document.createElement("div");s.className="page-type-buttons",[{type:"text",label:"Текст"},{type:"code",label:"Код"},{type:"test",label:"Тест"}].forEach(u=>{const r=document.createElement("button");r.type="button",r.className="btn",r.textContent=u.label,r.addEventListener("click",()=>{g(u.type),s.remove()}),s.appendChild(r)}),e.appendChild(s)});const p=document.querySelector(".assignment-create__form");p&&p.addEventListener("submit",function(o){o.preventDefault(),console.log("Form submission started");const s=new FormData(p),n={title:s.get("title"),description:s.get("description"),subject_id:s.get("subject_id"),group_id:s.get("group_id"),max_score:parseInt(s.get("max_score")),deadline:s.get("deadline"),pages:[]};document.querySelectorAll(".assignment-create__page").forEach((t,a)=>{var S;const _=t.getAttribute("data-page-index")||a,d=(S=t.querySelector('input[name*="[type]"]'))==null?void 0:S.value;if(!d){console.error("Page type not found for page",a);return}const v={type:d,order:parseInt(_)+1,title:"",content:""},h=t.querySelector('input[name*="[title]"]');switch(h&&(v.title=h.value),d){case"text":const b=t.querySelector(".tinymce-editor");b&&b.id&&(window.tinymce&&window.tinymce.get(b.id)?v.content=window.tinymce.get(b.id).getContent():v.content=b.value);break;case"code":const q=t.querySelector(".CodeMirror");if(q&&q.CodeMirror)v.content=q.CodeMirror.getValue();else{const $=t.querySelector('textarea[name*="[html]"]'),C=t.querySelector('textarea[name*="[css]"]');$&&C?v.content={html:$.value,css:C.value}:v.content=""}break;case"test":const w=y(t);w&&(v.test=w);break}n.pages.push(v)}),console.log("Collected assignment data:",n);const i=["title","description","subject_id","group_id","max_score","deadline"].filter(t=>!n[t]);if(i.length>0){console.error("Missing required fields:",i),alert("Пожалуйста, заполните все обязательные поля: "+i.join(", "));return}if(n.pages.length===0){console.error("No pages found"),alert("Пожалуйста, добавьте хотя бы одну страницу");return}n.pages.forEach((t,a)=>{console.log(`Page ${a}:`,t),t.title||console.error(`Page ${a} missing title`),t.type||console.error(`Page ${a} missing type`),t.order||console.error(`Page ${a} missing order`)}),fetch(p.action,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")},body:JSON.stringify(n)}).then(t=>t.json()).then(t=>{t.success?(alert(t.message),window.location.href=t.redirect_url):(alert("Ошибка: "+t.message),t.errors&&console.error("Validation errors:",t.errors))}).catch(t=>{console.error("Error submitting form:",t),alert("Произошла ошибка при отправке формы")})});function y(o){console.log("Collecting test data from page element:",o);const s={title:"",description:"",time_limit:null,passing_score:60,max_attempts:1,shuffle_questions:!1,show_results:!0,questions:[]},n=o.querySelector('input[name*="[title]"]');n&&(s.title=n.value);const u=o.querySelector('textarea[name*="[description]"]');u&&(s.description=u.value);const r=o.querySelectorAll(".test-question");return console.log("Found questions:",r.length),r.forEach((i,t)=>{const a={question_text:"",type:"single",points:1,answers:[]},_=i.querySelector('textarea[name*="[text]"]');_&&(a.question_text=_.value);const d=i.querySelector(".question-type");if(d&&(a.type=d.value),["single","multiple"].includes(a.type)){const v=i.querySelectorAll(".test-answer");console.log("Found answers for question",t,":",v.length),v.forEach((h,S)=>{const b={answer_text:"",is_correct:!1},q=h.querySelector('input[name*="[text]"]');q&&(b.answer_text=q.value);const w=h.querySelector(".answer-correct");w&&(b.is_correct=w.checked),a.answers.push(b)})}s.questions.push(a)}),console.log("Collected test data:",s),s}window.clearSelection=function(){const o=document.getElementById("subject-group"),s=document.getElementById("group-group"),n=document.querySelector('input[name="subject_id"][type="hidden"]'),u=document.querySelector('input[name="group_id"][type="hidden"]');o&&(o.style.display="block"),s&&(s.style.display="block"),n&&n.remove(),u&&u.remove();const r=document.getElementById("subject_id"),i=document.getElementById("group_id");r&&(r.value="",r.required=!0),i&&(i.value="",i.required=!0);const t=document.querySelector(".assignment-create__info");t&&(t.style.display="none")}});
