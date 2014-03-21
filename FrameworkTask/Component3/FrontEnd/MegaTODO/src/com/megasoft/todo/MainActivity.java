package com.megasoft.todo;

import org.json.JSONException;
import org.json.JSONObject;


import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

public class MainActivity extends Activity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        SharedPreferences config = getSharedPreferences("AppConfig", 0);
        //aw whatever l login activity 3ndna esmaha a
        final Intent intent = new Intent(this, LoginActivity2.class);

        if(!config.contains("sessionId")){
           startActivity(intent);
        }
        final String sessionId = config.getString("sessionId", null);
        
        setContentView(R.layout.activity_main);
        Button create = (Button) findViewById(R.id.button1);
        final EditText text   = (EditText) findViewById(R.id.editText);
        final JSONObject obj = new JSONObject();

        create.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String listName = text.getText().toString();
                try {
                    JSONObject json = new JSONObject();
                    json.put("text", listName);
                    //we might enter the initial tasks for the list in this phase as well
                    json.put("sessionId", sessionId);
                    (new HTTPPostRequest(){

                        public void onPostExecute() {
                        	//was ist das??
                            Log.d(" ", " ");
                        }

                    }).execute(json.toString(), "/lists");

                } catch (JSONException ex) {
                    ex.printStackTrace();
                }

            }
        });
        
    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }
    
}
