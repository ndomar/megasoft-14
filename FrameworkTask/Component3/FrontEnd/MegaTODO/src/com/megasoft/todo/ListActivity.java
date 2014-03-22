package com.megasoft.todo;

import android.os.Bundle;
import android.app.Activity;
import android.app.ActionBar.LayoutParams;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;



import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.example.megatodo.R;
import com.megasoft.todo.http.HTTPDeleteRequest;
import com.megasoft.todo.http.HTTPGetRequest;
import com.megasoft.todo.http.HTTPPostRequest;
import com.megasoft.todo.http.HTTPPutRequest;

public class ListActivity extends Activity {

	private SharedPreferences config;
	
	private void redirectToLogin() {
		Intent intent = new Intent(this, LoginActivity.class);
		startActivity(intent);
	}
	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_list);
        config = getSharedPreferences("AppConfig", 0);

        if(!config.contains("sessionId")){
            redirectToLogin();
         }
        final String sessionId = config.getString("sessionId", null);
        Intent i = getIntent();
        final String listId = i.getStringExtra("ID");
        final Activity self = this;
        final LinearLayout parentLayout = (LinearLayout) findViewById(R.id.mainLayout);
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
	        			final LinearLayout layout = new LinearLayout(self);
	        			layout.setOrientation(LinearLayout.HORIZONTAL);
	        			layout.setLayoutParams(new LinearLayout.LayoutParams(LayoutParams.WRAP_CONTENT, 
	        					LayoutParams.WRAP_CONTENT));
	                    JSONObject obj2 = jsonArray.getJSONObject(i);
	                    final EditText text =  new EditText(self);
	                    text.setText(obj2.getString("name"));
	                    text.setBackgroundColor(Color.TRANSPARENT);
	                    text.setTag(obj2.getString("id"));
	                    text.addTextChangedListener(new TextWatcher() {

	                        public void afterTextChanged(Editable s) {
	                        	JSONObject obj3 = null;
								try {
									obj3 = new JSONObject(res);
									obj3.put("name",text.getText());
								} catch (JSONException e) {
									e.printStackTrace();
								}
								(new HTTPPutRequest(){
	                                
									protected void onPostExecute(String res) {
                                		Log.d("megatodo", "text saved !");
									}
									
                                }).execute(obj3.toString(), "/lists/" + listId +"/tasks/"+text.getTag(), sessionId);
	                        	
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
	                                (new HTTPDeleteRequest(){
	                                
	                                	protected void onPostExecute(String res) {
	                                		layout.removeView(b);
										}
	                                	
	                                }).execute("/lists/" + listId +"/tasks/"+text.getId(), sessionId);

	                            } catch (JSONException ex) {
	                                ex.printStackTrace();
	                            }

	                        }
	                    });
	                    layout.addView(text);
	                    layout.addView(b);
	                    parentLayout.addView(layout);
	                    
	                }
				} catch (JSONException e) {
					e.printStackTrace();
				}
        	}
        }).execute("/lists/"+listId, sessionId);   
    }
    
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }
    
    public void logout(MenuItem m) {
    	config.edit().remove("sessionId").commit();
    	redirectToLogin();
    }
}
