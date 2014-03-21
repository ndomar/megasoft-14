package com.megasoft.todo;

import android.os.Bundle;
import android.app.Activity;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Menu;
import android.view.View;
import android.view.ViewGroup;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.widget.Button;
import android.widget.EditText;



import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.example.megatodo.R;
import com.megasoft.todo.http.HTTPGetRequest;
import com.megasoft.todo.http.HTTPPostRequest;

public class ListActivity extends Activity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_list);
        SharedPreferences config = getSharedPreferences("AppConfig", 0);
       // Intent intent = new Intent(this, LoginActivity.class);
        if(!config.contains("sessionId")){
     //       startActivity(intent);
         }
        final String sessionId = config.getString("sessionId", null);
        Intent i = getIntent();
        final String listId = i.getStringExtra("ID");
        final Activity self = this;
        JSONObject json = new JSONObject();
        try {
			json.put("sessionId", sessionId);
		} catch (JSONException e) {
			e.printStackTrace();
		}

        (new HTTPGetRequest(){
        
        	protected void onPostExecute(final String res) {
        		JSONObject obj = null;
				try {
					obj = new JSONObject(res);
				} catch (JSONException e1) {
					e1.printStackTrace();
				}

        		JSONArray jsonArray;
				try {
					jsonArray = (JSONArray) obj.get("tasks");
				
	        		for (int i = 0; i < jsonArray.length(); i++) {
	                    JSONObject obj2 = jsonArray.getJSONObject(i);
	                    final EditText text =  new EditText(self);
	                    text.setText(obj2.getString("text"));
	                    text.setBackgroundColor(Color.TRANSPARENT);
	                    text.setId(i);
	                    text.addTextChangedListener(new TextWatcher() {

	                        public void afterTextChanged(Editable s) {
	                        	JSONObject obj3 = null;
								try {
									obj3 = new JSONObject(res);
									obj3.put("text",text.getText());
								} catch (JSONException e) {
									e.printStackTrace();
								}
								(new HTTPPostRequest(){//should be put
	                                
                                }).execute(obj3.toString(), "/lists/" + listId +"/"+text.getId());
	                        	
	                        }

	                        public void beforeTextChanged(CharSequence s, int start, int count, int after) {}
	                        public void onTextChanged(CharSequence s, int start, int before, int count) {}
	                     });
	                    final Button b = new Button(self);
	                    b.setId(i);
	                    b.setText("Remove");
	                    b.setOnClickListener(new View.OnClickListener() {
	                        @Override
	                        public void onClick(View view) {
	                            try {
	                                JSONObject json = new JSONObject();
	                                json.put("sessionId", sessionId);
	                                ViewGroup layout = (ViewGroup) b.getParent();
	                                layout.removeView(b);
	                                (new HTTPPostRequest(){//should be delete
	                                
	                                }).execute(json.toString(), "/lists/" + listId +"/"+text.getId());

	                            } catch (JSONException ex) {
	                                ex.printStackTrace();
	                            }

	                        }
	                    });
	                    
	                }
				} catch (JSONException e) {
					e.printStackTrace();
				}
        	}
        }).execute(json.toString(), "/lists/"+listId);
        
       
        
    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.list, menu);
        return true;
    }
    
}
