var dialer = {
  view: 'dialpad',
}
$(function () {
  // toggleCallTimer(true);
  var speakerDevices = document.getElementById('speaker-devices');
  var ringtoneDevices = document.getElementById('ringtone-devices');
  var outputVolumeBar = document.getElementById('output-volume');
  var inputVolumeBar = document.getElementById('input-volume');
  var volumeIndicators = document.getElementById('volume-indicators');

  var device;
 
  log('Requesting Capability Token...');
  $.getJSON('api/get_token.php', function (data) {
    console.log(data);
    log('Capability Token: ' + data.token);
      console.log('Token: ' + data.token);

      // Setup Twilio.Device
      device = new Twilio.Device(data.token, {
        // Set Opus as our preferred codec. Opus generally performs better, requiring less bandwidth and
        // providing better audio quality in restrained network conditions. Opus will be default in 2.0.
        codecPreferences: ["opus", "pcmu"],
        // Use fake DTMF tones client-side. Real tones are still sent to the other end of the call,
        // but the client-side DTMF tones are fake. This prevents the local mic capturing the DTMF tone
        // a second time and sending the tone twice. This will be default in 2.0.
        fakeLocalDTMF: true,
        // Use `enableRingingState` to enable the device to emit the `ringing`
        // state. The TwiML backend also needs to have the attribute
        // `answerOnBridge` also set to true in the `Dial` verb. This option
        // changes the behavior of the SDK to consider a call `ringing` starting
        // from the connection to the TwiML backend to when the recipient of
        // the `Dial` verb answers.
        enableRingingState: true
      });

      device.on("ready", function(device) {
        log("Twilio.Device Ready!");
        document.getElementById("call-controls").style.display = "none"; /// Amjad did this.
      });

      device.on("error", function(error) {
        log("Twilio.Device Error: " + error.message);
      });

      device.on("connect", function(conn) {
        log("Successfully established call!");
        volumeIndicators.style.display = "block";
        dialer.incall = true;
        showCallButtons();
        check_call_started(conn.parameters.CallSid);
        $("button").click(function(){
          
        });
        bindVolumeIndicators(conn);
      });

      device.on("disconnect", function(conn) {
        log("Call ended.");
        // document.getElementById("button-call").style.display = "inline";
        // document.getElementById("button-hangup").style.display = "none";
        volumeIndicators.style.display = "none";
        dialer.direction = '';
        dialer.view = 'dialer';
        dialer.number = '';
        dialer.incall = false;
        toggleCallTimer();
        switchView();
      });

      device.on("incoming", function(conn) {
          console.log("Incoming connection from " + conn.parameters.From);
          dialer.direction = 'incoming';
          dialer.view = 'calling';
          dialer.number = conn.parameters.From;
          dialer.conn = conn;
          switchView();
      });

      setClientNameUI(data.identity);

      device.audio.on("deviceChange", updateAllDevices.bind(device));

      // Show audio selection UI if it is supported by the browser.
      if (device.audio.isOutputSelectionSupported) {
        document.getElementById("output-selection").style.display = "block";
      }
    })
    .catch(function (err) {
      console.log(err);
      log("Could not get a token from server!");
    });

  // Bind button to make call
  document.getElementById("button-call").onclick = function() {
    // get the phone number to connect the call to
    if (dialer.direction === 'incoming') {
      dialer.conn.accept();
      dialer.incall = true;
      return;
    }
    var params = {
      To: document.getElementById("phone-number").value
    };
    if(!params.To){return;}
      console.log("Calling " + params.To + "...");
    if (device) {
      var outgoingConnection = device.connect(params);
       outgoingConnection.on("ringing", function() {
        log("Ringing...");
        dialer.direction = 'calling';
        dialer.view = 'calling';
        dialer.number = params.To;
        switchView();
      });
    }
  };

  // Bind button to hangup call
  document.getElementById("button-hangup").onclick = function() {
    log("Hanging up...");
    console.log("Hanging up...");
    device.activeConnection().reject();
    dialer.direction = '';
    dialer.view = 'dialer';
    dialer.number = '';
    dialer.incall = false;
    toggleCallTimer();
    switchView();
    if (device) {
      device.disconnectAll();
    }
  };

  document.getElementById("get-devices").onclick = function() {
    navigator.mediaDevices
      .getUserMedia({ audio: true })
      .then(updateAllDevices.bind(device));
  };

  speakerDevices.addEventListener("change", function() {
    var selectedDevices = [].slice
      .call(speakerDevices.children)
      .filter(function(node) {
        return node.selected;
      })
      .map(function(node) {
        return node.getAttribute("data-id");
      });

    device.audio.speakerDevices.set(selectedDevices);
  });

  ringtoneDevices.addEventListener("change", function() {
    var selectedDevices = [].slice
      .call(ringtoneDevices.children)
      .filter(function(node) {
        return node.selected;
      })
      .map(function(node) {
        return node.getAttribute("data-id");
      });

    device.audio.ringtoneDevices.set(selectedDevices);
  });

  function bindVolumeIndicators(connection) {
    connection.on("volume", function(inputVolume, outputVolume) {
      var inputColor = "red";
      if (inputVolume < 0.5) {
        inputColor = "green";
      } else if (inputVolume < 0.75) {
        inputColor = "yellow";
      }

      inputVolumeBar.style.width = Math.floor(inputVolume * 300) + "px";
      inputVolumeBar.style.background = inputColor;

      var outputColor = "red";
      if (outputVolume < 0.5) {
        outputColor = "green";
      } else if (outputVolume < 0.75) {
        outputColor = "yellow";
      }

      outputVolumeBar.style.width = Math.floor(outputVolume * 300) + "px";
      outputVolumeBar.style.background = outputColor;
    });
  }

  function updateAllDevices() {
    updateDevices(speakerDevices, device.audio.speakerDevices.get());
    updateDevices(ringtoneDevices, device.audio.ringtoneDevices.get());
  }

  // Update the available ringtone and speaker devices
  function updateDevices(selectEl, selectedDevices) {
    selectEl.innerHTML = "";

    device.audio.availableOutputDevices.forEach(function(device, id) {
      var isActive = selectedDevices.size === 0 && id === "default";
      selectedDevices.forEach(function(device) {
        if (device.deviceId === id) {
          isActive = true;
        }
      });

      var option = document.createElement("option");
      option.label = device.label;
      option.setAttribute("data-id", id);
      if (isActive) {
        option.setAttribute("selected", "selected");
      }
      selectEl.appendChild(option);
    });
  }

  // Activity log
  function log(message) {
    var logDiv = document.getElementById("log");
    logDiv.innerHTML += "<p>&gt;&nbsp;" + message + "</p>";
    logDiv.scrollTop = logDiv.scrollHeight;
  }

  // Set the client name in the UI
  function setClientNameUI(clientName) {
    var div = document.getElementById("client-name");
    div.innerHTML = "Your client name: <strong>" + clientName + "</strong>";
  }
  function check_call_started(call_sid){
    $.get("api/get_record.php?call_sid="+call_sid, function(status){
      if(status == 1){
        toggleCallTimer(true);
      }else{
        setTimeout(function(){
          check_call_started(call_sid);
        }, 2000);
      }
    });
    
  }
  function switchView(){
    console.log(dialer);
    if(dialer.direction == 'incoming'){
      document.getElementById("diale_btn").click();
    }  

      $(".dialpad-screen").addClass('hidden');
      $("#dialpad-"+dialer.view).removeClass('hidden');
      if(dialer.number && dialer.view != 'dialer'){
        $("#display-number").html(dialer.number);
        $("#call-direction").html(dialer.direction || 'Calling');
      }
      showCallButtons();
  }
  function showCallButtons(){
    var btn = '';
    if(dialer.direction){
      if(dialer.direction == 'calling' || dialer.incall){
        btn = 'reject';
      }
    }else{
      btn = 'accept';
    }
    if(btn){
      $(".call-btn").hide();
      $(".call-"+btn).show();
    }else{
      $(".call-btn").show();
    }
  }
  function toggleCallTimer(start){
    console.log('timer started');
    dialer.time = 0;
    var diaplayTime = '';
    if(start){
      var currentTime;
      var h,m,s;
      dialer.timer = setInterval(() => {
        dialer.time++;
        $("#dialer-timer").html(getTimeSpent(dialer.time));
      }, 1000);
      $("#call-direction").html('Connected');
    }else{
      $("#dialer-timer").html('');
      $("#call-direction").html('');
      clearInterval(dialer.timer);
    }
  }
  function addPrefixZero(num){
    if(!num){
      num = 0;
    }
    return num < 10 ? '0'+num : num;
  }
  function getTimeSpent(s){
    // var s = Math.floor(ms / 1000);
    var unit = 60;
    var m = Math.floor(s / unit);
    if(m){
      s = s%unit;
    }
    var h = Math.floor(m / unit);
    if(h){
      m = m%unit;
    }
    var dTime = addPrefixZero(h)+':'+addPrefixZero(m)+':'+addPrefixZero(s);
    return dTime;
    console.log('dTime', dTime);
  }
});
