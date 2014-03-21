package com.megasoft.todo;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.example.megatodo.R;
import com.megasoft.todo.http.HTTPDeleteRequest;
import com.megasoft.todo.http.HTTPGetRequest;
import com.megasoft.todo.http.HTTPPostRequest;

import android.os.Bundle;
import android.app.ActionBar.LayoutParams;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.text.Layout;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

public class MainActivity extends Activity {

	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        SharedPreferences config = getSharedPreferences("AppConfig", 0);
        //aw whatever l login activity 3ndna esmaha a
        final Intent intent = new Intent(this, LoginActivity.class);

//        if(!config.contains("sessionId")){
//           startActivity(intent);
//        }
//        final String sessionId = config.getString("sessionId", null);
        
        setContentView(R.layout.activity_main);
        Button create = (Button) findViewById(R.id.button1);
        final EditText text   = (EditText) findViewById(R.id.editText);
        final JSONObject obj = new JSONObject();
        final Activity self = this;
        final LinearLayout listsLayout = (LinearLayout) findViewById(R.id.listsLayout);
        (new HTTPGetRequest(){

            public void onPostExecute(String response) {
            	try {
					JSONArray jsonArray = new JSONArray(response);
					for (int i = 0; i < jsonArray.length(); i++) {
						addElement(jsonArray.getJSONObject(i).getString("name"), jsonArray.getJSONObject(i).getString("id"), listsLayout);
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}	
            }

        }).execute("/lists");
        

        create.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                final String listName = text.getText().toString();
                try {
                    JSONObject json = new JSONObject();
                    json.put("text", listName);
                    Log.e("megatodo", text.getText().toString());
//                    json.put("sessionId", sessionId);
                    (new HTTPPostRequest(){

                        public void onPostExecute(String response) {
                        	Log.e("megatodo", response);
                        	JSONObject json;
							try {
								json = new JSONObject(response);
                        	addElement(listName, json.getString("id"), listsLayout);
							} catch (JSONException e) {
								// TODO Auto-generated catch block
								e.printStackTrace();
							}
                        }

                    }).execute(json.toString(), "/lists");

                } catch (JSONException ex) {
                    ex.printStackTrace();
                }

            }
        });
        
    }
    
    


    private void addElement(String string, String id, LinearLayout parentLayout) {
    	final LinearLayout layout = new LinearLayout(this);
    	final String listId = id;
		layout.setOrientation(LinearLayout.HORIZONTAL);
		layout.setLayoutParams(new LinearLayout.LayoutParams(LayoutParams.WRAP_CONTENT, 
				LayoutParams.WRAP_CONTENT));
		final TextView text = new TextView(this);
		text.setText(string);
		text.setOnClickListener(new OnClickListener() {
		
			@Override
			public void onClick(View v) {
				viewList(listId);
				
			}
		});
		Button button = new Button(this);
		button.setLayoutParams(new LinearLayout.LayoutParams(LayoutParams.WRAP_CONTENT, 
				LayoutParams.WRAP_CONTENT));
		//store the id in the button for later use
		button.setTag(id);
		button.setText("remove");
		layout.addView(text);
		layout.addView(button);
		parentLayout.addView(layout);
		
		button.setOnClickListener(new OnClickListener() {
           
            public void onClick(View view) {
                final String listName = text.getText().toString();
                try {
                    JSONObject json = new JSONObject();
                    json.put("text", listName);
                    Log.e("del", text.getText().toString());
//                    
                    (new HTTPDeleteRequest(){

                        public void onPostExecute(String response) {
                        	Log.e("megatodo", response);
                        	JSONObject json;
							try {
								json = new JSONObject(response);
                        	    layout.removeAllViews();
							} catch (JSONException e) {
								
								e.printStackTrace();
							}
                        }

                    }).execute(json.toString(), "/lists");

                } catch (JSONException ex) {
                    ex.printStackTrace();
                }

            }
        
		});
    }
    
    private void viewList(String id) {
    	Intent intent = new Intent(this, ListActivity.class);
    	intent.putExtra("ID", id);
    	startActivity(intent);
    }
    
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }
}
