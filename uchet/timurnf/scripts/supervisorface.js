      function savebaseproc(url)
      {
          var data = {
              id: 0
          };
          var baseproc=document.getElementById('baseprocid777').value;
          var baseball=document.getElementById('baseball777').value;
          data.baseproc= baseproc;
          data.baseball=baseball;
          data.operation = 'savebaseproc';
          $.ajax({
              type:'POST',
              url:url,
              data: data,
              timeout:20000,
              success:function(html){
                  location.reload();
                  $('body').css('cursor','default');
              },
              error:function(html){
                  $('body').css('cursor','default');
                  alert('Ошибка отображения! Перезагрузите страницу');
              }
          });
      }

      function save_user(id,name,password,type,div_id, bonus=0, manager=0, active=1, procent=0, koef=0){
          $('body').css('cursor','wait');
          data = {
              'id':id,
              'name':name,
              'password':password,
              'type':type,
              'bonus':bonus,
              'active':active,
              'procent':procent,
              'manageroperator':manager,
              'koef':koef,
              'operation':'save_user'
          };
          if (type==0){
              data['email'] = $('#'+div_id).find('.u_email').get(0).value;
              data['use_course'] = $('#'+div_id).find('.use_course').get(0).checked?1:0;
              if (document.getElementById("vorkvk"+id).checked == true) data['vorkvk'] = 1; else data['vorkvk'] = 0;

              document.getElementById("vorkvk"+id).value

              if ($('#'+div_id).find('.currency_id').length > 0){
                  data['currency_id'] = $('#'+div_id).find('.currency_id').get(0).value;
              }
              data['by_percent'] = $('#'+div_id).find('.by_percent').get(0).checked?1:0;
              data['percent_val'] = $('#'+div_id).find('.percent_val').get(0).value;

              data['shown'] = $('#'+div_id).find('.shown').get(0).checked?1:0;
              data['id_m_city'] = $('#'+div_id).find('.id_m_city').get(0).value;
              data['koef'] = $('#'+div_id).find('.koefic').get(0).value;
              obj = $('#'+div_id).find('.id_manager').get(0);
              data['id_manager'] = obj.options[obj.selectedIndex].value;
              obj = $('#'+div_id).find('.id_marketolog').get(0);
              data['id_marketolog'] = obj.options[obj.selectedIndex].value;
              obj = $('#'+div_id).find('.id_topmanager').get(0);
              data['id_topmanager'] = obj.options[obj.selectedIndex].value;
              obj = $('#'+div_id).find('.id_uchenik').get(0);
              data['id_uchenik'] = obj.options[obj.selectedIndex].value;
              arr_big = [];
              $('#'+div_id).find('.proc').each(function(k,o){
                  if ($(o).find('.p_name').length>0){
                      arr = {};
                      arr['id'] = $(o).find('.p_id').get(0).value;
                      arr['name'] = $(o).find('.p_name').get(0).value;
                      arr['price'] = $(o).find('.p_price').get(0).value;
                      arr['comission'] = $(o).find('.p_comission').get(0).value;
                      arr['bonus'] = 0;
                      arr['balls'] = 0;
                      arr['topmanager_bonus'] = 0;
                      arr['scores'] = $(o).find('.p_scores').get(0).checked?1:0;
                      arr['archiv'] = $(o).find('.p_archiv').get(0).checked?0:1;
                      arr['sortproc'] = $(o).find('.p_sortproc').get(0).value;
                      arr['div_id'] = o.id;
                      arr_big[k] = arr;
                  }
              });
              data['proc'] = arr_big;
          }
          if (type==4){
              data['bonus1'] = $('#'+div_id).find('.u_bonus1').val();
              data['bonus2'] = $('#'+div_id).find('.u_bonus2').val();
          }
          $.ajax({
              type:'POST',
              url:'index.php',
              data:data,
              timeout:20000,
      success:function(html){
      $('body').css('cursor','default');
      re = /\|/;
      if (!re.test(html)){
        if (html!='0'){
      $('#'+div_id).find('.u_id').get(0).value = html;
        }else{
          $('#'+div_id).remove();
      }
      }else{
          arr = html.split('|');
          html = arr[0];
          if (html!='0'){
          $('#'+div_id).find('.u_id').get(0).value = html;
          $('#'+div_id).find('.use_course').get(0).id='use_course'+html;
          $('#'+div_id).find('.u_label').get(0).setAttribute('for','use_course'+html);
          for(i=0;i<arr.length;i++){
          if (i==0)continue;
          if (arr[i]=='')continue;
          t = arr[i];
          arr1 = t.split('/');
          p_div_id = arr1[0];
          p_id = arr1[1];
          $('#'+p_div_id).find('.p_id').get(0).value=p_id;
          $('#'+p_div_id).find('.p_scores').get(0).id='scores'+p_id;
          $('#'+p_div_id).find('.p_label').get(0).setAttribute('for','scores'+p_id);
          if (p_id==0)$('#'+p_div_id).remove();
      }
      }else{
          $('#'+div_id).remove();
      }
      }
      if (type!=0){
      update_user_list();
      update_shop_list();
      }
      alert('Изменения сохранены!');
      location.reload();
      },
      error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка сохранения!');
      }
      });
      return false;
      }