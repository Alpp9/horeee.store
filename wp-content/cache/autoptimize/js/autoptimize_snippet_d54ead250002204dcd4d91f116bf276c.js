const chatGPT=document.querySelector('#yaia-chat-gpt');if(chatGPT){const form=chatGPT.querySelector('form')
const chatContainer=chatGPT.querySelector('#chat-container')
let loadInterval
function loader(element){element.textContent=''
loadInterval=setInterval(()=>{element.textContent+='.';if(element.textContent==='....'){element.textContent='';}},300);}
function typeText(element,text){let index=0
let interval=setInterval(()=>{if(index<text.length){element.innerHTML+=text.charAt(index)
index++}else{clearInterval(interval)}},20)}
function generateUniqueId(){const timestamp=Date.now();const randomNumber=Math.random();const hexadecimalString=randomNumber.toString(16);return`id-${timestamp}-${hexadecimalString}`;}
function chatStripe(isAi,value,uniqueId){return(`
            <div class="wrapper ${isAi && 'ai'}">
                <div class="chat">
                    <div class="profile">
                        <img 
                            src=${isAi ? YAIA.bot : YAIA.user} 
                            alt="${isAi ? 'bot' : 'user'}" 
                        />
                    </div>
                    <div class="message" id=${uniqueId}>${value}</div>
                </div>
            </div>
        `)}
const handleSubmit=async(e)=>{e.preventDefault()
const data=new FormData(form)
chatContainer.innerHTML+=chatStripe(false,data.get('prompt'))
form.reset()
const uniqueId=generateUniqueId()
chatContainer.innerHTML+=chatStripe(true," ",uniqueId)
chatContainer.scrollTop=chatContainer.scrollHeight;const messageDiv=document.getElementById(uniqueId)
loader(messageDiv)
let formData=new FormData();formData.append('prompt',data.get('prompt'));formData.append('action','yaiaChatGPT');const response=await fetch(YAIA.apiUrl,{method:'POST',body:formData})
clearInterval(loadInterval)
messageDiv.innerHTML=" "
let parsedData;if(response.ok){const data=await response.json();if(!data.success){if(Array.isArray(data.data)){parsedData=data.data.join("\r\n");}else{parsedData=data.message;}}else{parsedData=data.message;}
typeText(messageDiv,parsedData.trim())}else{typeText(messageDiv,(await response.text()).trim())}}
form.addEventListener('submit',handleSubmit)
form.addEventListener('keyup',(e)=>{if(e.keyCode===13){handleSubmit(e)}})};